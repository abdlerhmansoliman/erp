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
     Schema::create('purchase_items', callback: function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('tax_amount', 5, 2)->default(0.00);
            $table->decimal('discount_amount', 5, 2)->default(0.00);
            $table->decimal('total_price', 10, 2); 
            $table->decimal('net_price', 10, 2)->nullable(); 
            $table->foreignId('tax_id')->nullable()->constrained('taxes')->onDelete('cascade');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
