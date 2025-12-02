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
        Schema::create('parking_spots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->onDelete('restrict');
            $table->string('number', 10)->unique();
            $table->decimal('hourly_price', 8, 2);
            $table->decimal('width', 5, 2)->comment('Largura em metros');
            $table->decimal('length', 5, 2)->comment('Comprimento em metros');
            $table->enum('status', ['available', 'occupied', 'maintenance', 'reserved'])->default('available');
            $table->timestamps();

            $table->index('status');
            $table->index('operator_id');
            $table->index('number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_spots');
    }
};
