<?php

namespace Database\Seeders;

use App\Models\SalesInvoice;
use Illuminate\Database\Seeder;

class TestSalesInvoiceSeeder extends Seeder
{
    public function run()
    {
        SalesInvoice::create([
            'customer_id' => 6,
            'status' => 'ordered',
            'sub_total' => 1000.00,
            'tax_amount' => 140.00,
            'payment_status' => 'due',
            'grand_total' => 1140.00,
            'warehouse_id' => 2,
            'discount_amount' => 0.00,
            'shipping_cost' => 0.00,
        ]);
    }
}
