<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->decimal('sub_total', 10, 2)->default(0)->after('invoice_number');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('sub_total');
            $table->decimal('grand_total', 10, 2)->default(0)->after('tax_amount');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('grand_total');  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            //
        });
    }
};
