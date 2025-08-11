<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Role management
            'view-roles',
            'create-roles', 
            'edit-roles',
            'delete-roles',
            
            // User management
            'view-users',
            'create-users',
            'edit-users', 
            'delete-users',
            'manage-user-roles',
            
            // product management
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            'publish-products',
            
            // System administration
            'view-dashboard',
            'manage-settings',
            'view-logs',
            'backup-system',
        ];

        foreach ($permissions as $permission) {
    Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        
        // Super Admin - has all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);        $superAdminRole->givePermissionTo(Permission::all());
        
        // Admin - has most permissions except system critical ones
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo([
            'view-roles', 'create-roles', 'edit-roles',
            'view-users', 'create-users', 'edit-users', 'manage-user-roles',
            'view-products', 'create-products', 'edit-products', 'delete-products', 'publish-products',
            'view-dashboard', 'manage-settings'
        ]);
        
        // Editor - content management focused
        $editorRole = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editorRole->givePermissionTo([
            'view-products', 'create-products', 'edit-products', 'publish-products',
            'view-dashboard'
        ]);
        
        // Author - limited content creation
        $authorRole = Role::firstOrCreate(['name' => 'author', 'guard_name' => 'web']);
        $authorRole->givePermissionTo([
            'view-products', 'create-products', 'edit-products',
            'view-dashboard'
        ]);
        
        // User - basic permissions
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $userRole->givePermissionTo([
            'view-products', 'view-dashboard'
        ]);

        // Create default super admin user if it doesn't exist
        $superAdmin = User::firstOrCreate([
            'email' => 'admin@example.com'
        ], [
            'name' => 'Super Admin',
            'password' => bcrypt('password'),
            
        ]);
        
        $superAdmin->assignRole('super-admin');
        
        // Create sample users for testing
        $admin = User::firstOrCreate([
            'email' => 'admin-user@example.com'
        ], [
            'name' => 'Admin User',
            'password' => bcrypt('password'),
            
        ]);
        $admin->assignRole('admin');
        
        $editor = User::firstOrCreate([
            'email' => 'editor@example.com'
        ], [
            'name' => 'Editor User', 
            'password' => bcrypt('password'),
            
        ]);
        $editor->assignRole('editor');
        
        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Super Admin: admin@example.com / password');
        $this->command->info('Admin: admin-user@example.com / password');
        $this->command->info('Editor: editor@example.com / password');
    }
}