<?php 

namespace App\Repositories;

use App\Models\PurchaseItems;
use App\Models\SalesItem;
use App\Repositories\Interfaces\PurchaseItemRepositoryInterface;
use App\Repositories\Interfaces\SalesItemRepositoryInterface;

class SalesItemRepository implements SalesItemRepositoryInterface
{
    public function deleteByInvoice(int $invoiceId)
{
        SalesItem::where('purchase_invoice_id', $invoiceId)->delete();
    }

    public function getByInvoice(int $invoiceId)
    {
        return SalesItem::where('purchase_invoice_id', $invoiceId)->get();
    }

    public function create(array $data)
    {
        return SalesItem::create($data);
    }

    public function bulkInsert(array $rows)
    {
        if (!empty($rows)) {
            SalesItem::insert($rows);
        }
    }

}