<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class StripePaymentMethodSeeder extends Seeder
{
    public function run()
    {
        PaymentMethod::create([
            'name' => 'Stripe',
            'description' => 'Credit card payment via Stripe',
            'is_active' => true,
            'code' => 'stripe'
        ]);
    }
}