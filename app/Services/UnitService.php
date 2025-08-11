<?php


namespace App\Services;

use App\Repositories\Interfaces\UnitRepositoryInterface;

class UnitService
{
    protected $unitRepository;

    public function __construct(UnitRepositoryInterface $unitRepository)
    {
        $this->unitRepository = $unitRepository;
    }

    public function getAllUnits()
    {
        return $this->unitRepository->getAll();
    }

    public function getUnitById($id)
    {
        return $this->unitRepository->getById($id);
    }

    public function createUnit($data)
    {
        return $this->unitRepository->create($data);
    }

    public function updateUnit($id, $data)
    {
        return $this->unitRepository->update($id, $data);
    }

    public function deleteUnit($id)
    {
        return $this->unitRepository->delete($id);
    }
}
