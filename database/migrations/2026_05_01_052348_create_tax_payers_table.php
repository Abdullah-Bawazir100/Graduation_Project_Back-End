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
        Schema::create('tax_payers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained('files')->cascadeOnDelete();

            $table->string('trade_name')->unique();
            $table->string('commercial_record')->nullable();
            $table->string('activity_license')->nullable();
            $table->string('trade_pict')->nullable();
            $table->string('insurance_card')->nullable();
            $table->string('property_doc_pict')->nullable();
            $table->enum('file_type' , ['Individual', 'Company', 'CharitableCompany'])->default('Individual');
            $table->string('source')->nullable();

            $table->foreignId('district_id')->constrained('districts');
            $table->foreignId('region_id')->constrained('regions');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_payers');
    }
};
