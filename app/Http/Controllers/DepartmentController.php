<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use App\Services\DepartmentService;
class DepartmentController extends Controller
{
    protected $departmentService;
    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }
    public function index()
    {
        $departments=$this->departmentService->getAllDeprtment();
        return response()->json([
            'status' => 'success',
            'data' => $departments
        ]);
    }   
    public function show($id){
        $department=$this->departmentService->getDepartmentById($id);
        if(!$department){
            return response()->json([
                'status' => 'error',
                'message' => 'Department not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $department
        ]);
    }
    public function store(StoreDepartmentRequest $request)
    {
        $date=$request->validated();
        $department=$this->departmentService->createDepartment($date);
        return response()->json([
            'status' => 'success',
            'data' => $department
        ]);

    }
    public function update(UpdateDepartmentRequest $request, $id)
    
    {
        $date=$request->validated();
        $dapartment=$this->departmentService->updateDepartment($id, $date);
        if(!$dapartment){
            return response()->json([
                'status' => 'error',
                'message' => 'Department not found'
            ], 404);            
        }
        return response()->json([
            'status' => 'success',
            'data' => $dapartment
        ]);
    }
    public function destroy($id)
    {
        $department=$this->departmentService->deleteDepartment($id);
        if(! $department){
            return response()->json([
                'status' => 'error',
                'message' => 'Department not found'
            ], 404);        
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Department deleted successfully'
        ]);
    }
}