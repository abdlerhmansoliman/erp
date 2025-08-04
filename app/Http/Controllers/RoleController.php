<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Services\RoleService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
        public function __construct(
        private RoleService $roleService
    ) {}

    public function index()
    {
        try{
            $data=$this->roleService->getAllRolesWithPermissions();
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Error fetching roles and permissions',
                'error' => $e->getMessage()
            ],500);  
    }
    }
    public function show($id){
               try {
            $role = $this->roleService->getRoleById($id);
            
            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $role
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching role',
                'error' => $e->getMessage()
            ], 500);
        }
    }
        public function store(RoleRequest $request): JsonResponse
    {
        try {
            $role = $this->roleService->createRole($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'data' => $role
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating role',
                'error' => $e->getMessage()
            ], 422);
        }
    }
        public function update(RoleRequest $request, int $id): JsonResponse
    {
        try {
            $role = $this->roleService->updateRole($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'data' => $role
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating role',
                'error' => $e->getMessage()
            ], 422);
        }
    }
        public function assignPermissions(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);
        
        try {
            $role = $this->roleService->assignPermissionsToRole($id, $request->permissions);
            
            return response()->json([
                'success' => true,
                'message' => 'Permissions assigned successfully',
                'data' => $role
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning permissions',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
