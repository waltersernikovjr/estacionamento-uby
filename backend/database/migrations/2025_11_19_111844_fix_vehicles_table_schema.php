<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('brand', 50)->after('customer_id');
            $table->string('license_plate', 10)->nullable()->after('brand');
            $table->enum('type', ['car', 'motorcycle', 'truck', 'van'])->after('color');
        });
        
        DB::statement('UPDATE vehicles SET license_plate = plate');
        
        Schema::table('vehicles', function (Blueprint $table) {
            $table->unique('license_plate');
            $table->dropUnique(['plate']);
            $table->dropColumn(['plate', 'year']);
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('plate', 10)->unique()->after('customer_id');
            $table->unsignedSmallInteger('year')->after('color');
        });
        
        DB::statement('UPDATE vehicles SET plate = license_plate');
        
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['license_plate', 'brand', 'type']);
        });
    }
};
