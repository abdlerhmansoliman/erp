<?php 

namespace App\Repositories;


use App\Repositories\Interfaces\RoleRepositoryInterface;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class RoleRepository implements RoleRepositoryInterface
{
    public function getAllWithPermissions()
    {
        return Role::with('permissions')->get();
    }
    public function findById(int $id) {
        return Role::with('permissions')->find($id);
    }
    public function findByName(string $name) {
        return Role::where('name', $name)->first();
    }
    public function create(array $data){
        return Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web'
        ]);
    }
    public function update(int $id ,array $data){
        $role=Role::findById($id);
        $role->update([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web'
        ]);
    }
    public function delete(int $id){
        $role=Role::findById($id);
        $role->permissions()->detach();
    }
    public function syncPermissions(int $roleId, array $permissions){
        $role=Role::findById($roleId);
        $role->permissions()->sync($permissions);
    }
    public function getAllPermissions(){
        return Permission::all();
    }
        public function getRolePermissions(int $roleId)
    {
        $role = Role::findOrFail($roleId);
        return $role->permissions;
    }
}