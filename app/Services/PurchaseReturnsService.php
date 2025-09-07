<?php

namespace App\Services;

use App\Models\PurchaseReturn;
use App\Repositories\PurchaseInvoiceRepository;
use App\Repositories\PurchaseReturnRepository;
use Illuminate\Support\Facades\DB;

class PurchaseReturnsService
{
    public function __construct(
        protected PurchaseReturnRepository $returnRepo,
        protected PurchaseInvoiceRepository $invoiceRepo,
        protected StockService $stockService,
    ) {}

    public function getAllReturns(array $filters)
    {
        return $this->returnRepo->all($filters);
    }
    public function getReturnById($id)
    {
        return $this->returnRepo->findByIdWithItems($id);
    }

public function createReturn(array $data)
{
    return DB::transaction(function () use ($data) {

        $invoice = $this->invoiceRepo->findByIdWithItems($data['purchase_invoice_id']);

        $return = $this->returnRepo->create([
            'purchase_invoice_id' => $invoice->id,
            'supplier_id'         => $invoice->supplier_id,
            'warehouse_id'        => $invoice->warehouse_id,
            'status'              => 'draft',
            'return_date'         => now(),
            'sub_total'           => $data['sub_total'] ?? 0,
            'tax_amount'          => $data['tax_amount'] ?? 0,
            'discount_amount'     => $data['discount_amount'] ?? 0,
            'grand_total'         => $data['grand_total'] ?? 0,
            'note'                => $data['note'] ?? null,
            
        ]);

        foreach ($data['items'] as $item) {

            $invoiceItem = $invoice->items->firstWhere('product_id', $item['product_id']);
            if (!$invoiceItem || $item['quantity'] > $invoiceItem->quantity) {
                throw new \Exception("Quantity exceeds original invoice for product ID {$item['product_id']}");
            }

            $unitPrice  = $invoiceItem->unit_price;
            $totalPrice = $unitPrice * $item['quantity'];
            $taxAmount  = ($invoiceItem->tax_amount / $invoiceItem->quantity) * $item['quantity'];

            $this->returnRepo->createItem([
                'purchase_returns_id' => $return->id,
                'product_id'          => $item['product_id'],
                'quantity'            => $item['quantity'],
                'unit_price'          => $unitPrice,
                'total_price'         => $totalPrice,
            ]);

            $this->stockService->create([
                'product_id'      => (int) $item['product_id'],
                'warehouse_id'    => $invoice->warehouse_id,
                'product_unit_id' => $item['product_unit_id'] ?? null,
                'qty'             => (int) $item['quantity'],
                'remaining'       => (int) $item['quantity'],
                'net_unit_price'  => $unitPrice,
                'model_id'        => $return->id,
                'model_type'      => PurchaseReturn::class,
            ]);
        }

        return $this->returnRepo->findByIdWithItems($return->id);
    });
}

}