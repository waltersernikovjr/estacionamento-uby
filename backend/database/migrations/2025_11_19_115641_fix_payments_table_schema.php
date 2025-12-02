<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'hours_parked')) {
                $table->dropColumn('hours_parked');
            }
            
            if (!Schema::hasColumn('payments', 'payment_method')) {
                $table->enum('payment_method', ['credit_card', 'debit_card', 'pix', 'cash', 'others'])
                      ->default('credit_card')
                      ->after('amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'hours_parked')) {
                $table->decimal('hours_parked', 10, 2)->after('amount');
            }
            
            if (Schema::hasColumn('payments', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });
    }
};
