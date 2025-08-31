<?php

namespace App\Services;

use App\Helpers\DiscountHelper;
use App\Helpers\TaxHelper;
use App\Models\InvoiceItem;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseItems;
use App\Models\Stock;
use App\Repositories\Interfaces\PurchaseInvoiceRepositoryInterface;
use App\Repositories\PurchaseInvoiceRepository;
use App\Repositories\PurchaseItemRepository;
use App\Services\Billing\InvoiceCalculator;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceService
{

    public function __construct(
        protected PurchaseInvoiceRepository $invoiceRepo,
        protected PurchaseItemRepository $itemRepo,
        protected StockService $stockService,
        protected SupplierService $supplierService,
        protected WarehouseService $warehouseService,
    ) {}
    public function getAllInvoices(array $filters )
    {
        return $this->invoiceRepo->all($filters);
    }
    public function getInvoiceById($id)
    {
        return $this->invoiceRepo->findById($id);
    }
        public function getCreateData()
    {
        $suppliers = $this->supplierService->getSuppliers();
        $warehouses = $this->warehouseService->getWarehouses();

        $statuses = [
            ['key' => 'draft', 'label' => 'Draft'],
            ['key' => 'ordered', 'label' => 'Ordered'],
            ['key' => 'received', 'label' => 'Received'],
        ];

        return [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
            'statuses' => $statuses,
            'date' => now()->toDateString(),
        ];
    }
  public function createInvoice(array $data)
    {
        return DB::transaction(function () use ($data) {


            $invoice = $this->invoiceRepo->create([
                'supplier_id'    => $data['supplier_id'],
                'status'         => $data['status'] ?? 'draft',
                'sub_total'      => $data['sub_total'] ?? 0,
                'warehouse_id'    => $data['warehouse_id'],
                'discount_amount'=> $data['discount_amount'] ?? 0,
                'tax_amount'     => $data['tax_amount'] ?? 0,
                'grand_total'    => $data['grand_total'] ?? 0,
                'total_amount'   => $data['total_amount'] ?? 0,
            ]);

            $rows=collect($data['items'])->map(function ($item) use ($invoice) {
                $this->stockService->create([
                'product_id'      => (int) $item['product_id'],
                'warehouse_id'    => $invoice->warehouse_id,
                'product_unit_id' => $item['product_unit_id'] ?? null,
                'qty'             => (int) $item['quantity'],
                'remaining'       => (int) $item['quantity'],
                'net_unit_price'  => (float) $item['unit_price'],
                'model_id'        => $invoice->id,
                'model_type'      => PurchaseInvoice::class,
            ]); 
                return [
                'purchase_invoice_id' => $invoice->id,
                'product_id'          => (int) $item['product_id'],
                'quantity'            => (int) $item['quantity'],
                'unit_price'          => (float) $item['unit_price'],
                'discount_amount'     => (float) ($item['discount_amount'] ?? 0),
                'tax_id'              => $item['tax_id'] ?? null,
                'tax_amount'          => (float) ($item['tax_amount'] ?? 0),
                'total_price'         => (float) $item['total_price'],
                'net_price'           => (float) $item['net_price'],
                'created_at'          => now(),
            ];
            });

            $this->itemRepo->bulkInsert($rows->toArray());
            return $this->invoiceRepo->findByIdWithItems($invoice->id);
        });
    }
    
    public function deletePurchase($id)
    {
        return DB::transaction(function () use ($id) {
            $this->invoiceRepo->deleteItems($id);
            return $this->invoiceRepo->delete($id);
        });
    }
    public function deleteMultiplePurchases(array $ids)
    {
        return DB::transaction(function () use ($ids) {
            foreach ($ids as $id) {
                $this->invoiceRepo->deleteItems($id);
                $this->invoiceRepo->delete($id);
            }
            return true;
        });
    }
    public function getInvoiceByIdWithItems($id)
    {
        return $this->invoiceRepo->findByIdWithItems($id);
    }
}
