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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('position_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('address')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->enum('status', ['active', 'inactive', 'terminated'])->default('active');
            $table->date('birth_date');
            $table->date('hire_date');
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('national_id')->unique();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
