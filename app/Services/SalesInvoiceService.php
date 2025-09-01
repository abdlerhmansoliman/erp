<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\SalesInvoice;
use App\Repositories\Interfaces\SalesInvoiceRepositoryInterface;
use App\Repositories\PurchaseItemRepository;
use App\Repositories\SalesInvoiceRepository;
use App\Repositories\SalesItemRepository;
use App\Services\Billing\InvoiceCalculator;
use Illuminate\Support\Facades\DB;

class SalesInvoiceService
{
    /**
     * Create a new class instance.
     */
    protected $salesInvoiceRepository;
    protected $invoiceItemService;
    
    public function __construct(SalesInvoiceRepository $salesInvoiceRepository , 
    InvoiecItemService $invoiceItemService , 
     protected StockService $stockService,
     protected SalesItemRepository $itemRepo,
     protected CustomerService $customerService,
     protected WarehouseService $warehouseService
     )
    {
        $this->salesInvoiceRepository = $salesInvoiceRepository;
        $this->invoiceItemService=$invoiceItemService;
    }
    public function getAllInvoices()
    {
        return $this->salesInvoiceRepository->all();
    }
    public function getInvoiceById($id)
    {
        return $this->salesInvoiceRepository->findById($id);
    }
            public function getCreateData()
    {
        
        $customers = $this->customerService->getCustomers();
        $warehouses = $this->warehouseService->getWarehouses();
        $statuses = [
            ['key' => 'draft', 'label' => 'Draft'],
            ['key' => 'ordered', 'label' => 'Ordered'],
            ['key' => 'received', 'label' => 'Received'],
        ];

        return [
            'customers' => $customers,
            'warehouses' => $warehouses,
            'statuses' => $statuses,
            'date' => now()->toDateString(),
        ];
    }
    public function createInvoice(array $data)
    {
        return DB::transaction(function () use ($data) {
          $invoice=$this->salesInvoiceRepository->create([
            'customer_id' => $data['customer_id'],
            'status' => $data['status'] ?? 'draft',
            'sub_total' => $data['sub_total'] ?? 0,
            'warehouse_id' => $data['warehouse_id'],
            'discount_amount' => $data['discount_amount'] ?? 0,
            'tax_amount' => $data['tax_amount'] ?? 0,
            'grand_total' => $data['grand_total'] ?? 0,
          ]); 

          $rows=collect($data['items'])->map(function ($item) use ($invoice) {
            $this->stockService->create([
                'product_id'      => (int) $item['product_id'],
                'product_unit_id' => $item['product_unit_id'] ?? null,
                'warehouse_id'    => $invoice->warehouse_id,
                'qty'             => (int) $item['quantity'],
                'remaining'       => (int) $item['quantity'],
                'net_unit_price'  => (float) $item['unit_price'],
                'model_id'        => $invoice->id,
                'model_type'      => SalesInvoice::class,
              ]);
              return[
                'sales_invoice_id' => $invoice->id,
                'product_id'          => (int) $item['product_id'],
                'quantity'            => (int) $item['quantity'],
                'unit_price'          => (float) $item['unit_price'],
                'discount_amount'     => (float) ($item['discount_amount'] ?? 0),
                'tax_amount'          => (float) ($item['tax_amount'] ?? 0),
                'total_price'         => (float) $item['total_price'],
                'net_price'           => (float) $item['net_price'],
                'created_at'          => now(),
              ];
          });
          $this->itemRepo->bulkInsert($rows->toArray());
          return $invoice;
        });   
     }



    public function deleteInvoice($id)
    {
    return DB::transaction(function () use ($id) {
        $invoice = $this->salesInvoiceRepository->findById($id);
        if (!$invoice) return false;

        $this->invoiceItemService->deleteItemsForInvoice($invoice);

        return $this->salesInvoiceRepository->delete($id);
    });
    }
    
   public function getInvoiceByIdWithItems($id)
    {
        return $this->salesInvoiceRepository->findByIdWithItems($id);
    }
}
