<?php

namespace App\Services;

use App\Repositories\Interfaces\SalesInvoiceRepositoryInterface;
use App\Repositories\PurchaseItemRepository;
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
    public function __construct(SalesInvoiceRepositoryInterface $salesInvoiceRepository , 
    InvoiecItemService $invoiceItemService , 
     protected StockService $stockService,
             protected SalesItemRepository $itemRepo,

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
    public function createInvoice(array $data)
    {
        return DB::transaction(function () use ($data) {
            $invoice=$this->salesInvoiceRepository->create([
                'customer_id' => $data['customer_id'],
                'sub_total'   => 0,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'grand_total'  => 0,
                'invoice_number'=> $data['invoice_number'] ?? null,
            ]);
            $subTotalAfterProductDiscount = 0;
            $itemsProductDiscount = 0;
            $itemsProductTax = 0;
            $rows = [];
            foreach($data['items']as $item){
                $productId= (int)$item['product_id'];
                $qty= (float)$item['quantity'];
                $unitPrice= (float)$item['unit_price'];
                $warehouseId= (int)$item['warehouse_id'];

                $line=InvoiceCalculator::computeLine($productId,$qty,$unitPrice,'sales');
                $subTotalAfterProductDiscount+= $line['line_after_discount'];
                $itemsProductDiscount += $line['discount_amount'];
                $itemsProductTax += $line['tax_amount'];

                $this->stockService->decrease($productId, $warehouseId, $qty);

                $rows[] = [
                    'sales_invoice_id' => $invoice->id,
                    'product_id' => $productId,
                    'warehouse_id'=> $warehouseId,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'discount_amount' => $line['discount_amount'],
                    'tax_amount' => $line['tax_amount'],
                    'total_price'=> $line['line_base'],
                    'net_price' => $line['net_price'],
                    'created_at'=> now(),
                ];
            }
            $this->itemRepo->bulkInsert($rows);
            $totals=InvoiceCalculator::computeInvoiceTotals($subTotalAfterProductDiscount,
            $itemsProductTax,
            null,
            'sales');
            $invoice->update([
                'sub_total' => $subTotalAfterProductDiscount,
                'discount_amount' => $itemsProductDiscount + $totals['invoice_discount_amount'],
                'tax_amount' => $itemsProductTax + $totals['invoice_tax_amount'],
                'grand_total' => $totals['grand_total'],
            ]);
            return $invoice->load('customer', 'items.product');
        });   
     }

    public function updateInvoice($id, array $data)
    {
        return DB::transaction(function () use ($id,$data) {
            $invoice=$this->salesInvoiceRepository->findById($id);
            if (!$invoice) {
                abort(404, 'Invoice not found');
            }
            $oldItem=$this->itemRepo->getByInvoice($id);
            foreach ($oldItem as $old) {
                $this->stockService->increase($old->product_id, $old->warehouse_id, $old->quantity);
            }
            $this->itemRepo->deleteByInvoice($invoice->id);
            $subTotalAfterProductDiscount = 0;
            $itemsProductDiscount = 0;
            $itemsProductTax = 0;
            $rows = [];
            foreach ($data['items'] as $item) {
                $productId= (int) $item['product_id'];
                $qty  = (int) $item['quantity'];
                $unitPrice  = (float) $item['unit_price'];
                $warehouseId = (int) $item['warehouse_id'];

                $line = InvoiceCalculator::computeLine($productId, $qty, $unitPrice,'sales');
                $subTotalAfterProductDiscount += $line['line_after_discount'];
                $itemsProductDiscount += $line['discount_amount'];
                $itemsProductTax += $line['tax_amount'];

                $this->stockService->decrease($productId, $warehouseId, $qty);

                $rows[] = [
                'sales_invoice_id' => $invoice->id,
                'product_id'=> $productId,
                'warehouse_id' => $warehouseId,
                'quantity'=> $qty,
                'unit_price'=> $unitPrice,
                'discount_amount'=> $line['discount_amount'],
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
            'sales'
        );
        $invoice->update([
            'customer_id' => $data['customer_id'],
            'sub_total' => $subTotalAfterProductDiscount,
            'discount_amount' => $itemsProductDiscount + $totals['invoice_discount_amount'],
            'tax_amount' => $itemsProductTax + $totals['invoice_tax_amount'],
            'grand_total' => $totals['grand_total'],
            'invoice_number'=> $data['invoice_number'] ?? null,
        ]);
        return $invoice->load('customer', 'items.product');
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
    
   
}
