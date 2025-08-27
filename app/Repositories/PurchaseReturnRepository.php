<?php

namespace App\Repositories;

use App\Models\PurchaseReturn;

class PurchaseReturnRepository
{
    public function all()
    {
        return PurchaseReturn::with(['supplier', 'warehouse'])->get();
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