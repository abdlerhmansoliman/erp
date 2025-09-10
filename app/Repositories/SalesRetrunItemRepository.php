<?php

namespace App\Repositories;

use App\Models\SalesReturnItem;
use App\Repositories\Interfaces\SalesRetrunItemRepositoryInterface;

class SalesRetrunItemRepository implements SalesRetrunItemRepositoryInterface
{

    public function createItem(array $data)
    {
        SalesReturnItem::create($data);
    }
    public function all()
    {
        // TODO: implement
    }

    public function find($id)
    {
        // TODO: implement
    }

    public function create(array $data)
    {
        // TODO: implement
    }

    public function update($id, array $data)
    {
        // TODO: implement
    }

    public function delete($id)
    {
        // TODO: implement
    }
}
