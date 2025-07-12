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
    public function getAllInvoices()
    {
        return $this->purchaseInvoiceRepository->all();
    }
    public function getInvoiceById($id)
    {
        return $this->purchaseInvoiceRepository->findById($id);
    }
    public function createInvoice(array $data)
    {
        return DB::transaction(function()use($data) {
            $items=$data['items'];
            unset($data['items']);
            $total=$this->invoiceItemService->calculateTotal($items);
            $data['total_price']=$total;
            $invoice = $this->purchaseInvoiceRepository->create($data);
            foreach ($items as $item) {
                $this->invoiceItemService->createItemForInvoice($invoice, $item);
            }
            return $invoice->load('supplier','items');
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

    public function deleteInvoice($id)
    {
        return DB::transaction(function () use ($id) {
            $invoice = $this->purchaseInvoiceRepository->findById($id);
            if (!$invoice) return false;

            $this->invoiceItemService->deleteItemsForInvoice($invoice);
            return $this->purchaseInvoiceRepository->delete($id);
        });
    }
    
   
}
