<?php 
namespace App\Repositories;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;

class DepartmentRepository implements DepartmentRepositoryInterface
{
 
    public function all()
    {
        return Department::get();
    }

    public function find($id)
    {
        return Department::find($id);
    }

    public function create(array $data)
    {
        return Department::create($data);
    }
    public function update($id, array $data)
    {
        $department=$this->find($id);
        $department->update($data);
        return $department;
    }

    public function delete($id)
    {
        return Department::destroy($id);
    }



}


