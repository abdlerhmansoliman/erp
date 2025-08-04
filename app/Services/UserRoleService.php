<?php

namespace App\Services;



use App\Repositories\Interfaces\UserRoleRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;



class UserRoleService
{
    /**
     * Create a new class instance.
     */
    protected $userRoleRepository;
    public function __construct( UserRoleRepositoryInterface $userRoleRepository)
    {
        $this->userRoleRepository=$userRoleRepository;
    }
    public function getUserWithRolesAndPermissions(int $userId){
        return $this->userRoleRepository->getUserWithRolesAndPermissions($userId);
    }
    public function assignRolesToUser(int $userId, array $roles){
        return $this->userRoleRepository->assignRolesToUser($userId, $roles);
    }
    public function removeRole(int $userId, array $roles){
        return $this->userRoleRepository->removeRolesFromUser($userId, $roles);
    }
    public function syncUserRoles(int $userId, array $roles){
        return $this->userRoleRepository->syncUserRoles($userId, $roles);
    }
    public function assignPermissionsToUser(int $userId, array $permissions){
        return $this->userRoleRepository->assignPermissionsToUser($userId, $permissions);
    }
    public function syncUserPermissions(int $userId, array $permissions){
        return $this->userRoleRepository->syncUserPermissions($userId, $permissions);
    }
    public function getUsersByRole(string $roleName){
        return $this->userRoleRepository->getUsersByRole($roleName);
    }
    public function getUserRoles(int $userId){
        return $this->userRoleRepository->getUserRoles($userId);
    }
    public function getUserPermissions(int $userId){
        return $this->userRoleRepository->getUserPermissions($userId);
    }
    public function getUserDirectPermissions(int $userId){
        return $this->userRoleRepository->getUserDirectPermissions($userId);
    }
    public function getUserCompletePermissionData(int $userId)
    
    {
        $user = $this->getUserWithRolesAndPermissions($userId);
        
        if (!$user) {
            throw new ModelNotFoundException('User not found.');
        }
        
        return [
            'user' => $user,
            'roles' => $user->roles,
            'all_permissions' => $user->getAllPermissions(),
            'direct_permissions' => $user->getDirectPermissions(),
            'role_permissions' => $user->getPermissionsViaRoles()
        ];
    }
        public function bulkAssignRole(array $userIds, string $roleName)
    {
        $results = [];
        
        foreach ($userIds as $userId) {
            try {
                $results[$userId] = $this->assignRolesToUser($userId, [$roleName]);
            } catch (\Exception $e) {
                $results[$userId] = ['error' => $e->getMessage()];
            }
        }
        
        return $results;
    }

}
