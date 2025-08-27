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
    /**
     * Create a new class instance.
     */
//   protected $purchaseInvoiceRepository;
//     protected InvoiecItemService $invoiceItemService;

//     public function __construct( purchaseInvoiceRepositoryInterface $purchaseInvoiceRepository, InvoiecItemService $invoiceItemService)
//      {
//         $this->purchaseInvoiceRepository = $purchaseInvoiceRepository;
//         $this->invoiceItemService = $invoiceItemService;
//     }
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

            $rows = [];

            foreach ($data['items'] as $item) {

                $productId   = (int) $item['product_id'];
                $qty         = (int) $item['quantity'];
                $unitPrice   = (float) $item['unit_price'];
                $discount_amount    = (float) $item['discount_amount'] ?? 0;
                $tax_id     = $item['tax_id'] ?? null;
                $tax_amount = (float) $item['tax_amount'] ?? 0;
                $total_price = (float) $item['total_price'];
                $net_price   = (float) $item['net_price'];

                $this->stockService->create([
                'product_id'    => $productId,
                'warehouse_id'  => $invoice->warehouse_id,
                'product_unit_id'=> $item['product_unit_id'] ?? null,
                'qty'           => $qty,
                'remaining'     => $qty, 
                'net_unit_price'=> $unitPrice,
                'model_id'      => $invoice->id,
                'model_type'    => PurchaseInvoice::class,
                ]);

                $rows[] = [
                    'purchase_invoice_id' => $invoice->id,
                    'product_id'          => $productId,
                    'quantity'            => $qty,
                    'unit_price'          => $unitPrice,
                    'discount_amount'     => $discount_amount,
                    'tax_id'             => $tax_id,
                    'tax_amount'          => $tax_amount,
                    'total_price'         => $total_price,
                    'net_price'           => $net_price,
                    'created_at'          => now(),
                ];
            }

            $this->itemRepo->bulkInsert($rows);

            // 3. إعادة الفاتورة مع العناصر
            return $this->invoiceRepo->findByIdWithItems($invoice->id);
        });
    }
    


public function updateInvoice(int $id, array $data)
{
    return DB::transaction(function () use ($id, $data) {

        $invoice = $this->invoiceRepo->findWithItems($id);
        if (!$invoice) {
            abort(404, 'Invoice not found');
        }

        $oldItems = $this->itemRepo->getByInvoice($invoice->id);
        foreach ($oldItems as $old) {
            $this->stockService->decrease($old->product_id, $old->warehouse_id, $old->quantity);
        }

        $this->itemRepo->deleteByInvoice($invoice->id);

        $subTotalAfterProductDiscount = 0.0;
        $itemsProductDiscount = 0.0;
        $itemsProductTax = 0.0;

        $rows = [];

        foreach ($data['items'] as $item) {
            $productId= (int) $item['product_id'];
            $qty  = (int) $item['quantity'];
            $unitPrice  = (float) $item['unit_price'];
            $warehouseId = (int) $item['warehouse_id'];

            $line = InvoiceCalculator::computeLine($productId, $qty, $unitPrice,'purchase');

            $subTotalAfterProductDiscount+= $line['line_after_discount'];
            $itemsProductDiscount += $line['discount_amount'];
            $itemsProductTax+= $line['tax_amount'];

            $this->stockService->increase($productId, $warehouseId, $qty);

            $rows[] = [
                'purchase_invoice_id' => $invoice->id,
                'product_id'=> $productId,
                'warehouse_id' => $warehouseId,
                'quantity'=> $qty,
                'unit_price'=> $unitPrice,
                'discount_amount'=> $line['discount_amount'],
                'tax_id' => $item['tax_id'] ?? null,
                'tax_amount'=> $line['tax_amount'],
                'total_price'=> $line['line_base'],
                'net_price'=> $line['net_price'],
                'updated_at'=> now(),
            ];
        }

        $this->itemRepo->bulkInsert($rows);

        $totals = InvoiceCalculator::computeInvoiceTotals(
            $subTotalAfterProductDiscount,
            $itemsProductTax,
            $data['discount_id'] ?? null,
            'purchase'
        );

        $invoice ->update( [
            'supplier_id' => $data['supplier_id']     ?? $invoice->supplier_id,
            'status' => $data['status']          ?? $invoice->status,
            'invoice_number' => $data['invoice_number']  ?? $invoice->invoice_number,
            'sub_total' => $subTotalAfterProductDiscount,
            'discount_amount' => $itemsProductDiscount + $totals['invoice_discount_amount'],
            'tax_amount' => $itemsProductTax + $totals['invoice_tax_amount'],
            'grand_total'=> $totals['grand_total'],
        ]);

            return $invoice->load('supplier', 'items.product');
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
}
