<?php

namespace App\Services;

use App\Repositories\EmployeeRepository;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeService
{
    /**
     * Create a new class instance.
     */
   protected $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function getAllEmployees()
    {
        return $this->employeeRepository->all();
    }

    public function getEmployeeById($id)
    {
        return $this->employeeRepository->find($id);
    }

    public function createEmployee(array $data)
    {
        return $this->employeeRepository->create($data);
    }

    public function updateEmployee($id, array $data)
    {
        return $this->employeeRepository->update($id, $data);
    }

    public function deleteEmployee($id)
    {
        return $this->employeeRepository->delete($id);
    }
        public function getEmployeesByDepartment($id)
    {
        return $this->employeeRepository->getEmpolyeeByDepartment($id);
    }
    public function getEmployeesByPosition($id)
    {
        return $this->employeeRepository->getEmpolyeeByPosition($id);
    }
}
