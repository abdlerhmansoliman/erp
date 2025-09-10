<?php

namespace App\Repositories;

use App\Models\SalesReturnItem;
use App\Models\SalesReturn;
use App\Repositories\Interfaces\SalesRetrunRepositoryInterface;

class SalesRetrunRepository implements SalesRetrunRepositoryInterface
{
    public function all(array $filters)
    {
        return SalesReturn::query()->
        with(['invoice', 'invoice.customer', 'items', ])
        ->when($filters['search']?? null, function($q,$search){
            $q->where('invoice_number', 'like', "%$search%")
            ->orWhereHas('invoice', function($q2) use ($search){
                $q2->where('invoice_number', 'like', "%$search%");
            });
        })
        ->orderBy($filters['sortBy']??'id', $filters['sortDir']??'desc')
        ->paginate($filters['perPage']??10);
    }

    public function find($id)
    {
        // TODO: implement
    }

    public function create(array $data)
    {
       return SalesReturn::create($data);
    }
    
    public function update($id, array $data)
    {
        // TODO: implement
    }

    public function findByIdWithItems($id)
    {
        return SalesReturn::with(['items.product', 'warehouse', 'customer'])->find($id);
    }
    public function sumReturnedQuantity($invoiceId , $productId){
        return SalesReturnItem::whereHas('salesReturn',function($q) use ($invoiceId){
            $q->where('sales_invoice_id', $invoiceId);
        })
        ->where('product_id', $productId)
        ->sum('quantity');
    }
}
