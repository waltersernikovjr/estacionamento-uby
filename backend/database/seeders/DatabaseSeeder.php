<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Chamar seeders na ordem correta
        $this->call([
            ParkingSpotSeeder::class,
            // Adicionar outros seeders aqui conforme necessário
        ]);
    }
}
