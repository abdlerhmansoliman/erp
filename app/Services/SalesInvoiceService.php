<?php

namespace App\Services;

use App\Repositories\Interfaces\SalesInvoiceRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SalesInvoiceService
{
    /**
     * Create a new class instance.
     */
    protected $salesInvoiceRepository;
    protected $invoiceItemService;
    public function __construct(SalesInvoiceRepositoryInterface $salesInvoiceRepository , InvoiecItemService $invoiceItemService)
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
            $items = $data['items'];
            unset($data['items']);
            $total = $this->invoiceItemService->calculateTotal($items);
            $data['total_price'] = $total;
            $invoice = $this->salesInvoiceRepository->create($data);
            foreach ($items as $item) {
                $this->invoiceItemService->createItemForInvoice($invoice, $item);
            }
            return $invoice->load('customer', 'items.product');
        });   
     }

    public function updateInvoice($id, array $data)
    {
        return DB::transaction(function () use ($id,$data) {
            $item=$data['items'] ?? null;
            unset($data['items']);
            $invoice=$this->salesInvoiceRepository->update($id, $data);
            if($item){
            $this->invoiceItemService->deleteItemsForInvoice($invoice);
            foreach ($item as $itemData) {
                $this->invoiceItemService->createItemForInvoice($invoice, $itemData);
            }
        }
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
