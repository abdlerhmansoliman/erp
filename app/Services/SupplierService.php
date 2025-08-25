<?php

namespace App\Services;

use App\Models\Supplier;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use App\Repositories\SupplierRepository;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Hash;

class SupplierService
{
    /**
     * Create a new class instance.
     */
    protected $supplierRepository;
    public function __construct(SupplierRepositoryInterface $supplierRepository)
    {
        $this->supplierRepository=$supplierRepository;
    }
      public function getAll(array $filters)
    {
        return $this->supplierRepository->all($filters);
    }

    public function getById($id)
    {
        return $this->supplierRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->supplierRepository->create($data);
    }

    public function update(Supplier $supplier, array $data)
    {
        return $this->supplierRepository->update($supplier, $data);
    }


    public function delete(Supplier $supplier)
    {
        return $this->supplierRepository->delete($supplier);
    }

    public function deleteMultiple(array $ids): int
    {
        return $this->supplierRepository->deleteMultiple($ids);
    }
}
