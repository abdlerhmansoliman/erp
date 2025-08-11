<?php 

namespace App\Repositories;


use App\Models\User;
use App\Repositories\Interfaces\USerRoleRepositoryInterface;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class UserRoleRepository implements USerRoleRepositoryInterface
{
   public function getUserWithRolesAndPermissions(int $userId){
    return User::with(['roles', 'permissions'])->find($userId);
   }
    
    public function assignRolesToUser(int $userId, array $roles)
    {
        $user=User::findOrFail($userId);
        $user->assignRole($roles);
        return $user->fresh(['roles.permissions','permissions']);
    }
    public function removeRolesFromUser(int $userId, array $roles)
    {
        $user=User::findOrFail($userId);
        $user->removeRole($roles);
        return $user->fresh(['roles.permissions','permissions']);
    }
    public function syncUserRoles(int $userId, array $roles)
    {
        $user=User::findOrFail($userId);
        $user->syncRoles($roles);
        return $user->fresh(['roles.permissions','permissions']);
    }
    public function assignPermissionsToUser(int $userId, array $permissions)
    {
        $user=User::findOrFail($userId);
        $user->givePermissionTo($permissions);
        return $user->fresh(['roles.permissions','permissions']);
    }
    public function syncUserPermissions(int $userId, array $permissions)
    {
        $user=User::findOrFail($userId);
        $user->syncPermissions($permissions);
        return $user->fresh(['roles.permissions','permissions']);
    }
    public function getUsersByRole(string $roleName)
    {
        $user=User::role($roleName)->with(['roles', 'permissions'])->get();
    }
    public function getUserRoles(int $userId)
    {
        $user=User::findOrFail($userId);
        return $user->roles;

    }
    public function getUserPermissions(int $userId)
    {
        $user=User::findOrFail($userId);
        return $user->getAllPermissions();

    }
    public function getUserDirectPermissions(int $userId)
    {
        $user=User::findOrFail($userId);
        return $user->getDirectPermissions();
    }
}