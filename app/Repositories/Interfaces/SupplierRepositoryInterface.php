<?php
namespace App\Repositories\Interfaces;

use App\Models\Supplier;
use App\Models\User;
interface SupplierRepositoryInterface
{
public function all(array $filters);
public function find($id);
public function create(array $data);
public function update(Supplier $supplier, array $data);
public function delete(Supplier $supplier);
public function deleteMultiple(array $ids): int;
public function allSuppliers();
}