<?php

namespace App\Repositories;

use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;

class PurchaseReturnRepository
{
public function all(array $filters)
{
    return PurchaseReturn::query()
        ->with(['items', 'invoice', 'invoice.supplier'])
        ->when($filters['search'] ?? null, function ($q, $search) {
            $q->where('invoice_number', 'like', "%{$search}%") 
              ->orWhereHas('invoice', function ($q2) use ($search) {
                  $q2->where('invoice_number', 'like', "%{$search}%"); 
              });
        })
        ->orderBy($filters['sortBy'] ?? 'id', $filters['sortDir'] ?? 'desc')
        ->paginate($filters['perPage'] ?? 10);
}

    public function create(array $data)
    {
        return PurchaseReturn::create($data);
    }

    public function createItem(array $data)
    {
        return PurchaseReturnItem::create($data);
    }

    public function findByIdWithItems($id)
    {
        return PurchaseReturn::with(['items', 'invoice', 'invoice.supplier'])
            ->findOrFail($id);
    }

    public function deleteItems($returnId)
    {
        return PurchaseReturnItem::where('purchase_returns_id', $returnId)->delete();
    }

    public function delete($id)
    {
        return PurchaseReturn::where('id', $id)->delete();
    }

    public function bulkInsertItems(array $rows)
    {
        return PurchaseReturnItem::insert($rows);
    }
}
