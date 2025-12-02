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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cpf', 14)->unique();
            $table->string('rg', 20);
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();

            $table->string('address_zipcode', 9);
            $table->string('address_street');
            $table->string('address_number', 20);
            $table->string('address_complement')->nullable();
            $table->string('address_neighborhood');
            $table->string('address_city');
            $table->string('address_state', 2);

            $table->rememberToken();
            $table->timestamps();

            $table->index('email');
            $table->index('cpf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
