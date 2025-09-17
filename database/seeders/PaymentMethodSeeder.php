<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        PaymentMethod::insert([
            [
                'name' => 'Cash',
                'config' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bank',
                'config' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Card',
                'config' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Stripe',
                'config' => json_encode([
                    'secret' => env('STRIPE_SECRET'),
                    'publishable' => env('STRIPE_KEY'),
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
