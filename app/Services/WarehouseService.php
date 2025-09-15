<?php

namespace App\Services;

use App\Models\Warehouse;

use App\Repositories\WarehouseRepository;

class WarehouseService
{
    /**
     * Create a new class instance.
     */
    protected $warehouseRepository;
    public function __construct( WarehouseRepository $warehouse)
    {
        $this->warehouseRepository=$warehouse;
    }
    public function getAllWarehouses(array $filters){
        return $this->warehouseRepository->getAllWarehouses($filters);
    }

    public function getById($id){
        return $this->warehouseRepository->getById($id);
    }

    public function create(array $data){
        return $this->warehouseRepository->create($data);
    }

    public function update(Warehouse $warehouse, array $data){
        return $this->warehouseRepository->update($warehouse,$data);
    }
    
    public function delete(Warehouse $warehouse){
        return $this->warehouseRepository->delete($warehouse);
    }
    public function getWarehouses()
    {
        return $this->warehouseRepository->allWarehouses();
    }

    public function getByIdWithAvailableProducts($id, $filters )
    {
     return $this->warehouseRepository->getByIdWithAvailableProducts($id, $filters);
    }
}
