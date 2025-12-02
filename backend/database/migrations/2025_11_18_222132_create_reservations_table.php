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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->foreignId('vehicle_id')->constrained()->onDelete('restrict');
            $table->foreignId('parking_spot_id')->constrained()->onDelete('restrict');
            $table->timestamp('entry_time');
            $table->timestamp('exit_time')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();

            $table->index('customer_id');
            $table->index('vehicle_id');
            $table->index('parking_spot_id');
            $table->index('status');
            $table->index('entry_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
