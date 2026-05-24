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
        Schema::create('file_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained('files')->cascadeOnDelete();
            $table->foreignId('tax_collector_id')->constrained('tax_collectors')->cascadeOnDelete();

            $table->enum('status', ['InsideArchive', 'OutsideArchive' , 'Missing'])->default('InsideArchive');
            $table->string('date');

            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
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
        Schema::dropIfExists('file_movements');
    }
};
