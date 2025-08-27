<?php

namespace App\Repositories;

use App\Models\PurchaseReturnItem;

class PurchaseReturnItemRepository
{


    public function create(array $data)
    {
        return PurchaseReturnItem::create($data);
    }

    public function update(PurchaseReturnItem $item, array $data)
    {
        $item->update($data);
        return $item;
    }

    public function delete(PurchaseReturnItem $item)
    {
        return $item->delete();
    }
}