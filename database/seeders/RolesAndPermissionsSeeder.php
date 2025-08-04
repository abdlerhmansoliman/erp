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
            
            // Content management
            'view-posts',
            'create-posts',
            'edit-posts',
            'delete-posts',
            'publish-posts',
            
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
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo(Permission::all());
        
        // Admin - has most permissions except system critical ones
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'view-roles', 'create-roles', 'edit-roles',
            'view-users', 'create-users', 'edit-users', 'manage-user-roles',
            'view-posts', 'create-posts', 'edit-posts', 'delete-posts', 'publish-posts',
            'view-dashboard', 'manage-settings'
        ]);
        
        // Editor - content management focused
        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $editorRole->givePermissionTo([
            'view-posts', 'create-posts', 'edit-posts', 'publish-posts',
            'view-dashboard'
        ]);
        
        // Author - limited content creation
        $authorRole = Role::firstOrCreate(['name' => 'author']);
        $authorRole->givePermissionTo([
            'view-posts', 'create-posts', 'edit-posts',
            'view-dashboard'
        ]);
        
        // User - basic permissions
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->givePermissionTo([
            'view-posts', 'view-dashboard'
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