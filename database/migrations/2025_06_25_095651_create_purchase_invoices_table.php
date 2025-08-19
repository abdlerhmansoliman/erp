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
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['draft', 'ordered', 'received', 'cancelled'])->default('draft');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('invoice_number')->unique();
            $table->decimal('sub_total', 10, 2)->default(0)->after('status');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('sub_total');
            $table->decimal('discount_amount', 15, 2)->default(0);  
            $table->decimal('grand_total', 10, 2)->default(0)->after('tax_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
