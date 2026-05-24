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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('app_users')->cascadeOnDelete();

            $table->string('trade_name')->unique();
            $table->string('commercial_record')->nullable();
            $table->string('activity_license')->nullable();
            $table->string('trade_pict')->nullable();
            $table->string('insurance_card')->nullable();
            $table->string('property_doc_pict')->nullable();
            $table->enum('file_type' , ['Individual', 'Company', 'CharitableCompany'])->default('Individual');

            $table->string('articles_of_incorporation')->nullable();
            $table->string('govemor_license')->nullable();
            $table->string('partners_id_cards')->nullable();

            $table->string('by_laws_copy')->nullable();

            $table->enum('status' , ['Pending', 'Confirmed', 'Archived' , 'Rejected'])->default('Pending');
            $table->string('note')->nullable();
            $table->string('source')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
