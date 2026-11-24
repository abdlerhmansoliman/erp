<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class StripePaymentMethodSeeder extends Seeder
{
    public function run()
    {
        PaymentMethod::create([
            'name' => 'Stripe',
            'description' => 'Credit card payment via Stripe',
            'is_active' => true,
            'code' => 'stripe',
        ]);
    }
}
