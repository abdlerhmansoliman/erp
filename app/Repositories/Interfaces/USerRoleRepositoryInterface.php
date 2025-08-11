<?php
namespace App\Repositories\Interfaces;

use App\Models\User;
interface USerRoleRepositoryInterface
{
    public function getUserWithRolesAndPermissions(int $userId);
    
    public function assignRolesToUser(int $userId, array $roles);
    
    public function removeRolesFromUser(int $userId, array $roles);
    
    public function syncUserRoles(int $userId, array $roles);
    
    public function assignPermissionsToUser(int $userId, array $permissions);
    
    public function syncUserPermissions(int $userId, array $permissions);
    
    public function getUsersByRole(string $roleName);
    
    public function getUserRoles(int $userId);
    
    public function getUserPermissions(int $userId);
    
    public function getUserDirectPermissions(int $userId);
}