<?php

namespace App\Repositories;

use App\Models\PurchaseItems;
use App\Models\PurchaseReturn;

class PurchaseReturnRepository
{
    public function all($filters)
    {
        return PurchaseReturn::query()
            ->with['supplier']
            ->when($filters['search'] ?? null, function ($q, $search) {
                return $q->where('id','like',"%{$search}%")
                ->orWhereHas('supplier', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                });
            })
            ->orderBy($filters['sortBy'] ?? 'id', $filters['sortDir'] ?? 'desc')
            ->paginate($filters['perPage'] ?? 10);
    }

    public function find($id)
    {
        return PurchaseReturn::with(['supplier', 'warehouse','items'])->find($id);
    }

    public function create(array $data)
    {
        return PurchaseReturn::create($data);
    }

    public function update(PurchaseReturn $purchaseReturn, array $data)
    {
        $purchaseReturn->update($data);
        return $purchaseReturn;
    }

    public function delete(PurchaseReturn $purchaseReturn)
    {
       return $purchaseReturn->delete();
    }
}