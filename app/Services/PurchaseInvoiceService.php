<?php

namespace App\Services;

use App\Models\InvoiceItem;
use App\Repositories\Interfaces\PurchaseInvoiceRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceService
{
    /**
     * Create a new class instance.
     */
  protected $purchaseInvoiceRepository;
    protected InvoiecItemService $invoiceItemService;

    public function __construct( purchaseInvoiceRepositoryInterface $purchaseInvoiceRepository, InvoiecItemService $invoiceItemService)
     {
        $this->purchaseInvoiceRepository = $purchaseInvoiceRepository;
        $this->invoiceItemService = $invoiceItemService;
    }
    public function getAllInvoices(array $filters )
    {
        return $this->purchaseInvoiceRepository->all($filters);
    }
    public function getInvoiceById($id)
    {
        return $this->purchaseInvoiceRepository->findById($id);
    }
    public function createInvoice(array $data)
    {
        return DB::transaction(function()use($data) {
            
            $purchase=$this->purchaseInvoiceRepository->create([
                'supplier_id'=>$data['supplier_id'],
                'status'=>$data['status']??'pending',
                'total_amount' => 0
            ]);
            $total=0;
            foreach($data['items'] as $item){
                $total +=$item['quantity'] * $item['unit_price'];
            }
            $this->purchaseInvoiceRepository->addItems($purchase->id,$data['items']);
            $purchase->update([
                'total_amount' => $total,
            ]);

            return $purchase;
        });
    }

    public function updateInvoice($id, array $data)
    {
         return DB::transaction(function () use ($id, $data) {
            $items = $data['items'] ?? null;
            unset($data['items']);

            if ($items) {
                $total = $this->invoiceItemService->calculateTotal($items);
                $data['total_price'] = $total;
            }

                $invoice = $this->purchaseInvoiceRepository->update($id, $data);
            if ($items) {
                $this->invoiceItemService->deleteItemsForInvoice($invoice);

                foreach ($items as $item) {
                    $this->invoiceItemService->createItemForInvoice($invoice, $item);
                }
            }

            return $invoice->load('supplier', 'items.product');
        });

    }

    public function deletePurchase($id)
    {
        return DB::transaction(function () use ($id) {
            $this->purchaseInvoiceRepository->deleteItems($id);
            return $this->purchaseInvoiceRepository->delete($id);
        });
    }
}
