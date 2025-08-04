<?php

namespace App\Services;


use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use Illuminate\Validation\ValidationException;


class RoleService
{
    /**
     * Create a new class instance.
     */
    protected $roleRepository;
    public function __construct( RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository=$roleRepository;
    }
    public function getAllRolesWithPermissions(){
        return $this->roleRepository->getAllWithPermissions();
    }
    public function getRoleById(int $id){
        return $this->roleRepository->findById($id);
    }
    public function createRole(array $data){
        if($this->roleRepository->findByName($data['name'])){
            throw ValidationException::withMessages([
                'name' => 'Role name already exists'
            ]);
        }
        $role=$this->roleRepository->create($data);
        if(isset($data['permissions'])&& is_array($data['permissions'])){
            $this->roleRepository->syncPermissions($role->id,$data['permissions']);
        }
        return $role;
    }
    public function updateRole(array $data,int $id){
        $existingRole=$this->roleRepository->findById($id);
        if($existingRole && $existingRole->id!=$id){
            throw ValidationException::withMessages([
                'name' => 'Role name already exists'
            ]);
        }
        $role=$this->roleRepository->update($id,$data);
        if(isset($data['permissions'])&& is_array($data['permissions'])){
            $role=$this->roleRepository->syncPermissions($role->id,$data['permissions']);
        }
        return $role;
    }
    public function deteleRole(int $id){
        return $this->roleRepository->delete($id);
    }
    public function assignPermissionsToRole(int $roleId, array $permissions): Role
    {
        return $this->roleRepository->syncPermissions($roleId, $permissions);
    }
    
    public function getAllPermissions()
    {
        return $this->roleRepository->getAllPermissions();
    }
    
    public function getRolePermissions(int $roleId)
    {
        return $this->roleRepository->getRolePermissions($roleId);
    }
    
    public function getRolesAndPermissions()
    {
        return [
            'roles' => $this->getAllRolesWithPermissions(),
            'permissions' => $this->getAllPermissions()
        ];
    }
}
