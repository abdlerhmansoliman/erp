<?php

namespace App\Services;

use App\Helpers\DiscountHelper;
use App\Helpers\TaxHelper;
use App\Models\InvoiceItem;
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
        protected StockService $stockService
    ) {}
    public function getAllInvoices(array $filters )
    {
        return $this->invoiceRepo->all($filters);
    }
    public function getInvoiceById($id)
    {
        return $this->invoiceRepo->findById($id);
    }
  public function createInvoice(array $data)
    {
        return DB::transaction(function () use ($data) {

            $invoice = $this->invoiceRepo->create([
                'supplier_id' => $data['supplier_id'],
                'status'  => $data['status'] ?? 'pending',
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

            foreach ($data['items'] as $item) {
                $productId   = (int) $item['product_id'];
                $qty         = (int) $item['quantity'];
                $unitPrice   = (float) $item['unit_price'];
                $warehouseId = (int) $item['warehouse_id'];

                $line = InvoiceCalculator::computeLine($productId, $qty, $unitPrice,'purchase');

                $subTotalAfterProductDiscount+= $line['line_after_discount'];
                $itemsProductDiscount += $line['discount_amount'];
                $itemsProductTax+= $line['tax_amount'];

                $this->stockService->increase($productId, $warehouseId, $qty);

                $rows[] = [
                    'purchase_invoice_id' => $invoice->id,
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

            $totals = InvoiceCalculator::computeInvoiceTotals(
                $subTotalAfterProductDiscount,
                $itemsProductTax,
                $data['discount_id'] ?? null,
                'purchase'
            );

            $invoice ->update( [
                'sub_total' => $subTotalAfterProductDiscount,
                'discount_amount' => $itemsProductDiscount + $totals['invoice_discount_amount'],
                'tax_amount' => $itemsProductTax + $totals['invoice_tax_amount'],
                'grand_total' => $totals['grand_total'],
            ]);

            return $invoice->load('supplier', 'items.product');
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
}
