<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole=Role::where('name','admin')->first();
        $admin=User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'=>'Admin ',
                'password'=>bcrypt('password'),
            ]
            );
            $admin->assignRole($adminRole);
    }
}
