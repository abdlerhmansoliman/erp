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
        dd($invoice);
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
            $alreadyReturnedQty=$this->returnRepo->sumReturnedQuantity(
                $invoice->id,
                $item['product_id']);   
                $availableQty=$invoiceItem->quantity-$alreadyReturnedQty;
                if ($item['quantity'] > $availableQty) {
                    throw new \Exception("Return quantity exceeds available quantity for product ID {$item['product_id']}. Available: {$availableQty}");
                }
            $unitPrice  = $invoiceItem->unit_price;
            $totalPrice = $unitPrice * $item['quantity'];
            $this->returnRepo->createItem([
            'purchase_returns_id' => $return->id,
            'product_id'         => $item['product_id'],
            'quantity'           => $item['quantity'],
            'unit_price'         => $unitPrice,
            'total_price'        => $totalPrice,
            'tax_amount'         => $item['tax_amount'] ?? 0,
            'discount_amount'    => $item['discount_amount'] ?? 0,
            'tax_id'             => $invoiceItem->tax_id,
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
    public function prepareReturnData($id){
        $invoice=$this->invoiceRepo->findByIdWithItems($id);
        if(!$invoice){
            return null;
        }
        foreach($invoice->items as $item){
            $returnedQty = $this->returnRepo->sumReturnedQuantity($invoice->id, $item->product_id);
            $item->returned_quantity=$returnedQty;
            $item->available_quantity=$item->quantity-$returnedQty;
        }
        return $invoice;
    }
    public function showReturn($id){
        $invoice= $this->returnRepo->findByIdWithItems($id);
        foreach($invoice->items as $item){
            $returnedQty = $this->returnRepo->sumReturnedQuantity($invoice->id, $item->product_id);
            $item->returned_quantity=$returnedQty;
            $item->available_quantity=$item->quantity-$returnedQty;
        }
        return $invoice;
    }
}