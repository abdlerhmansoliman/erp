<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

abstract class BaseInvoiceService
{
    public function __construct(protected InvoiecItemService $invoiecItemService) {}
    protected  function handleCreate(array $data,$repository,string $invoiceType){
        return DB::transaction(function()use($data,$repository,$invoiceType){
            $items=$data['items'];
            unset($data['items']);
            $total=$this->invoiecItemService->calculateTotal($items);
            $data['total_price']=$total;
            $invoice=$repository->create($data);
            foreach ($items as $item){
                $this->invoiecItemService->createItemForInvoice($invoice,$item);
            }
            return $invoice->load('items.product');
        });
    }
    protected function handelUpdate($id,array $data , $repository,string $invoiceType){
        return DB::transaction(function() use($id,$data,$repository,$invoiceType){
            $items=$data['items'] ?? null;
            unset($data['items']);
            if ($items) {
            $total = $this->invoiecItemService->calculateTotal($items);
            $data['total_price'] = $total;
            }
            $repository->update($id,$data);
             $invoice = $repository->findById($id);
             
        if ($items) {
            $this->invoiecItemService->deleteItemsForInvoice($invoice);

            foreach ($items as $item) {
                $this->invoiecItemService->createItemForInvoice($invoice, $item);
            }

            return $invoice->load('items.product');
        }

        return $invoice->load('items.product');

        });
    }
}