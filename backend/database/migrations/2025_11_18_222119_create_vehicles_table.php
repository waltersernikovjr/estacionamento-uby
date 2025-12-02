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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('plate', 10)->unique();
            $table->string('model');
            $table->string('color', 50);
            $table->unsignedSmallInteger('year');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Índices
            $table->index('customer_id');
            $table->index('plate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
