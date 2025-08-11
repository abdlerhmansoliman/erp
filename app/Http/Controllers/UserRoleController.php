<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\UserRoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function __construct(
        private UserRoleService $userRoleService
    ){}
    
    public function getUserPermissions(int $userId): JsonResponse
    {
        try {
            $data = $this->userRoleService->getUserCompletePermissionData($userId);
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching user permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function assignRoles(Request $request, int $userId): JsonResponse
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'string|exists:roles,name'
        ]);
        
        try {
            $user = $this->userRoleService->assignRolesToUser($userId, $request->roles);
            
            return response()->json([
                'success' => true,
                'message' => 'Roles assigned successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning roles',
                'error' => $e->getMessage()
            ], 422);
        }
    }
    
    public function syncRoles(Request $request, int $userId): JsonResponse
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'string|exists:roles,name'
        ]);
        
        try {
            $user = $this->userRoleService->syncUserRoles($userId, $request->roles);
            
            return response()->json([
                'success' => true,
                'message' => 'Roles synchronized successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error synchronizing roles',
                'error' => $e->getMessage()
            ], 422);
        }
    }
    
    public function removeRoles(Request $request, int $userId): JsonResponse
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'string|exists:roles,name'
        ]);
        
        try {
            $user = $this->userRoleService->removeRole($userId, $request->roles);
            
            return response()->json([
                'success' => true,
                'message' => 'Roles removed successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing roles',
                'error' => $e->getMessage()
            ], 422);
        }
    }
    
    public function assignPermissions(Request $request, int $userId): JsonResponse
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);
        
        try {
            $user = $this->userRoleService->assignPermissionsToUser($userId, $request->permissions);
            
            return response()->json([
                'success' => true,
                'message' => 'Permissions assigned successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning permissions',
                'error' => $e->getMessage()
            ], 422);
        }
    }
    
    public function syncPermissions(Request $request, int $userId): JsonResponse
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);
        
        try {
            $user = $this->userRoleService->syncUserPermissions($userId, $request->permissions);
            
            return response()->json([
                'success' => true,
                'message' => 'Permissions synchronized successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error synchronizing permissions',
                'error' => $e->getMessage()
            ], 422);
        }
    }
    
    public function getUsersByRole(string $roleName): JsonResponse
    {
        try {
            $users = $this->userRoleService->getUsersByRole($roleName);
            
            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching users by role',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function bulkAssignRole(Request $request): JsonResponse
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
            'role' => 'required|string|exists:roles,name'
        ]);
        
        try {
            $results = $this->userRoleService->bulkAssignRole($request->user_ids, $request->role);
            
            return response()->json([
                'success' => true,
                'message' => 'Bulk role assignment completed',
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error in bulk role assignment',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}