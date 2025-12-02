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
        Schema::table('reservations', function (Blueprint $table) {
            $table->text('operator_notes')->nullable()->after('status');
            $table->foreignId('finalized_by_operator_id')
                ->nullable()
                ->after('operator_notes')
                ->constrained('operators')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['finalized_by_operator_id']);
            $table->dropColumn(['operator_notes', 'finalized_by_operator_id']);
        });
    }
};
