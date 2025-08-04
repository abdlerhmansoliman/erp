<?php
namespace App\Repositories\Interfaces;

use App\Models\User;
interface RoleRepositoryInterface
{
    public function getAllWithPermissions();
    
    public function findById(int $id);
    
    public function findByName(string $name);
    
    public function create(array $data);
    
    public function update(int $id, array $data);

    public function delete(int $id);
    
    public function syncPermissions(int $roleId, array $permissions);
    
    public function getAllPermissions();
    
    public function getRolePermissions(int $roleId);
}