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
        Schema::create('tax_informations', function (Blueprint $table) {
            $table->id();
            $table->string('tax_amount');
            $table->string('last_payment');

            $table->foreignId('tax_payer_id')->constrained('tax_payers')->cascadeOnDelete();
            $table->foreignId('tax_type_id')->constrained('tax_types')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_informations');
    }
};
