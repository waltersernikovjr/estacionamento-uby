<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parking_spots', function (Blueprint $table) {
            $table->foreignId('operator_id')->nullable()->change();
            $table->enum('type', ['regular', 'vip', 'disabled'])->default('regular')->after('number');
        });
    }

    public function down(): void
    {
        Schema::table('parking_spots', function (Blueprint $table) {
            $table->foreignId('operator_id')->nullable(false)->change();
            $table->dropColumn('type');
        });
    }
};
