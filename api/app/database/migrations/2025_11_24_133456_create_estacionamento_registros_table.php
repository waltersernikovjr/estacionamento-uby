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
        Schema::create('estacionamento_registros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vaga_id')->constrained()->cascadeOnDelete();
            $table->foreignId('veiculo_id')->constrained()->cascadeOnDelete();
            $table->dateTime('entrada');
            $table->dateTime('saida')->nullable();
            $table->decimal('valor_total', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estacionamento_registros');
    }
};
