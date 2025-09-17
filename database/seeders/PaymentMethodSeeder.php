<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    PaymentMethod::insert([
    ['name' => 'Cash', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Bank', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Card', 'created_at' => now(), 'updated_at' => now()],
    ]);
    }
}
