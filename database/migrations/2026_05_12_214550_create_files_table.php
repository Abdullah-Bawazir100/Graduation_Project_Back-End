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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('tax_number')->nullable();
            $table->string('inventory_number')->unique();
            $table->string('activity_start_date')->nullable();
            $table->integer('docs_count');
            $table->string('note')->nullable();
            $table->string('full_address');

            $table->foreignId('tax_payer_id')->constrained('tax_payers')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('file_status_id')->constrained('file_status');
            $table->foreignId('activity_type_id')->constrained('activity_types');
            $table->foreignId('payment_type_id')->constrained('payment_types');
            $table->foreignId('region_id')->constrained('regions');
            $table->foreignId('district_id')->constrained('districts');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('app_users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
