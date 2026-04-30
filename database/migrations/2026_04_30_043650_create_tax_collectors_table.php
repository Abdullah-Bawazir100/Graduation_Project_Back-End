<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_collectors', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('id_card'); // Path to the uploaded file
            $table->string('phone');
            $table->foreignId('job_type_id')->constrained('job_types');
            $table->foreignId('dept_id')->constrained('departments');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_collectors');
    }
};