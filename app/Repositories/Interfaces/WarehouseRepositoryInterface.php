<?php
namespace App\Repositories\Interfaces;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;

interface WarehouseRepositoryInterface
{
public function getAllWarehouses(array $filters);
public function getById($id);
public function create(array $data);
public function update(Warehouse $warehouse, array $data);
public function delete(Warehouse $warehouse);
public function allWarehouses();
}