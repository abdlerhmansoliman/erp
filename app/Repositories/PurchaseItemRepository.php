<?php 

namespace App\Repositories;

use App\Models\PurchaseItems;
use App\Repositories\Interfaces\PurchaseItemRepositoryInterface;



class PurchaseItemRepository implements PurchaseItemRepositoryInterface
{
    public function deleteByInvoice(int $invoiceId)
{
        PurchaseItems::where('purchase_invoice_id', $invoiceId)->delete();
    }

    public function getByInvoice(int $invoiceId)
    {
        return PurchaseItems::where('purchase_invoice_id', $invoiceId)->get();
    }

    public function create(array $data)
    {
        return PurchaseItems::create($data);
    }

    public function bulkInsert(array $rows)
    {
        if (!empty($rows)) {
            PurchaseItems::insert($rows);
        }
    }

}