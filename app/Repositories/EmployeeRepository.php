<?php 
namespace App\Repositories;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Repositories\Interfaces\EmployeeRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;

class EmployeeRepository implements EmployeeRepositoryInterface
{
 
    public function all()
    {
        return Employee::with('department','position','salaries','attendances')->get();
    }

    public function find($id)
    {
        return Employee::with('department','position','salaries','attendances')->find($id);
    }

    public function create(array $data)
    {
        return Employee::create($data);
    }
    public function update($id, array $data)
    {
        $employee=$this->find($id);
        $employee->update($data);
        return $employee;
    }

    public function delete($id)
    {
        return Employee::destroy($id);
    }
     public function getEmpolyeeByDepartment($id){
        return Employee::where('department_id', $id)->get();
        
    }
    public function getEmpolyeeByPosition($id)
    {
        return Employee::where('position_id', $id)->get();
    }
}


