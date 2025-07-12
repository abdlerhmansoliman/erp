<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $permissions=[
            'view users',
            'edit users',
            'delete users',
            'create posts',
            'edit posts',
            'delete posts',
            'view posts',
            
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
            ]);
        }
        $adminRole=Role::firstOrCreate([
            'name' => 'admin',
        ]);
        $employeeRole=Role::firstOrCreate([
            'name' => 'employee',
        ]);
        $adminRole->syncPermissions($permissions);
        $employeeRole->syncPermissions([
            'view users',
            'view posts',
        ]);

    }
}
