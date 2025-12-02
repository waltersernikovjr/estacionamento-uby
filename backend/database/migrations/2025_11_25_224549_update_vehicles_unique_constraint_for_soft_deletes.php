<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropUnique('vehicles_license_plate_unique');
        });

        DB::statement('CREATE UNIQUE INDEX vehicles_license_plate_unique ON vehicles (license_plate, deleted_at)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX vehicles_license_plate_unique ON vehicles');

        Schema::table('vehicles', function (Blueprint $table) {
            $table->unique('license_plate');
        });
    }
};
