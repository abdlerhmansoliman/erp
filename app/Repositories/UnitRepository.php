<?php

namespace App\Repositories;

use App\Models\Unit;
use App\Repositories\Interfaces\UnitRepositoryInterface;

class UnitRepository implements UnitRepositoryInterface
{
    public function getAll()
    {
        return Unit::all();
    }

    public function getById($id)
    {
        return Unit::findOrFail($id);
    }

    public function create(array $data)
    {
        return Unit::create($data);
    }

    public function update($id, array $data)
    {
        $unit = Unit::findOrFail($id);
        $unit->update($data);
        return $unit;
    }

    public function delete($id)
    {
        return Unit::destroy($id);
    }
}
