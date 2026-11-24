<?php

namespace App\Repositories\Interfaces;

use App\Models\Warehouse;

interface WarehouseRepositoryInterface
{
    public function getAllWarehouses(array $filters);

    public function getById($id);

    public function create(array $data);

    public function update(Warehouse $warehouse, array $data);

    public function delete(Warehouse $warehouse);

    public function allWarehouses();

    public function getByIdWithAvailableProducts($id, $filters);
}
