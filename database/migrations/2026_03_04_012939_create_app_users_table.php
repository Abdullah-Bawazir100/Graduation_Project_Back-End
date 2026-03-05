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
        Schema::create('app_users', function (Blueprint $table) {
            $table->id();

            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->string('id_card')->nullable(); // مسار ملف PDF
            $table->string('user_name')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->boolean('must_change_password')->default(true);
            
            $table->enum('role', ['Admin', 'Manager', 'Employee', 'Tax Payer'])->default('Employee');

            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('created_by')->nullable()->constrained('app_users')->nullOnDelete();
            $table->timestamps();
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_users');
    }
};
