<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Services\EmployeeService;
use Illuminate\Http\Request;

class EmployerController extends Controller
{
    protected $employeeService;
    public function __construct( EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }
    public function index(){
        $employee= $this->employeeService->getAllEmployees();
        return response()->json([
            'status' => 'success',
            'data' => $employee
        ]);
    }
    public function show($id){
        $employee=$this->employeeService->getEmployeeById($id);
        if(!$employee){
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found'
            ], 404);
            
        }
        return response()->json([
            'status' => 'success',
            'data' => $employee
        ]);
    }
    public function store(EmployeeRequest $request){
        $data=$request->validated();
        $employee=$this->employeeService->createEmployee($data);
        return response()->json([
            'status' =>'employee created successfully',
            'data' => $employee
        ]);
    }
    public function update(EmployeeUpdateRequest $request, $id){
        $data=$request->validated();
        $employee=$this->employeeService->updateEmployee($id, $data);
        if(!$employee){
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $employee
        ]);
    }
    public function destroy($id){
        $employee=$this->employeeService->deleteEmployee($id);
        if(!$employee){
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $employee
        ]);
    }
    public function getEmployeesByDepartment($id){
        $employees = $this->employeeService->getEmployeesByDepartment($id);
        if(!$employees){
            return response()->json([
                'status' => 'error',
                'message' => 'No employees found for this department'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $employees
        ]);
    }
    publix
    
}
