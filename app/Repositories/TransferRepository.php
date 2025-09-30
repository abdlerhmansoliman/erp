<?php

namespace App\Repositories;

use App\Models\Transfer;
use App\Repositories\Interfaces\TransferRepositoryInterface;

class TransferRepository implements TransferRepositoryInterface
{
public function all(array $filters = [])
{
    $query = Transfer::with(['fromWarehouse', 'toWarehouse', 'items.product']);

    if (!empty($filters['search'])) {
        $search = $filters['search'];
        $query->where(function ($q) use ($search) {
            $q->where('id', 'like', "%$search%")
              ->orWhere('notes', 'like', "%$search%");
        });
    }

    if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
        $query->whereBetween('transfer_date', [$filters['from_date'], $filters['to_date']]);
    }

    if (!empty($filters['from_warehouse_id'])) {
        $query->where('from_warehouse_id', $filters['from_warehouse_id']);
    }
    if (!empty($filters['to_warehouse_id'])) {
        $query->where('to_warehouse_id', $filters['to_warehouse_id']);
    }

    if (!empty($filters['status'])) {
        $query->where('status', $filters['status']);
    }

    $sortBy = $filters['sortBy'] ?? 'transfer_date';
    $sortDirection = $filters['sortDirection'] ?? 'desc';
    $query->orderBy($sortBy, $sortDirection);

    $perPage = $filters['perPage'] ?? 15;
    return $query->paginate($perPage);
}


    public function find($id)
    {
        return Transfer::with('items')->find($id);
    }

    public function create(array $data)
    {
        return Transfer::create($data);
    }

}
