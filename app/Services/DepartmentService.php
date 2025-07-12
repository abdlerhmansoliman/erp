<?php

namespace App\Services;

use App\Repositories\DepartmentRepository;


class DepartmentService
{
    /**
     * Create a new class instance.
     */
   protected $departmentRepository;

    public function __construct(DepartmentRepository $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    public function getAllDeprtment()
    {
        return $this->departmentRepository->all();
    }

    public function getDepartmentById($id)
    {
        return $this->departmentRepository->find($id);
    }

    public function createDepartment(array $data)
    {
        return $this->departmentRepository->create($data);
    }

    public function updateDepartment($id, array $data)
    {
        return $this->departmentRepository->update($id, $data);
    }

    public function deleteDepartment($id)
    {
        return $this->departmentRepository->delete($id);
    }

}
