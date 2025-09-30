<?php

namespace App\Repositories;

use App\Models\Transfer;
use App\Repositories\Interfaces\TransferRepositoryInterface;

class TransferRepository implements TransferRepositoryInterface
{
    public function all()
    {
        
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
