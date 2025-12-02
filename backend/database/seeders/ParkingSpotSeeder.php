<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Infrastructure\Persistence\Models\ParkingSpot;

class ParkingSpotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $existingCount = ParkingSpot::count();

        if ($existingCount > 0) {
            $this->command->warn("⚠️  Já existem {$existingCount} vagas no banco.");
            $this->command->info('💡 Use php artisan migrate:fresh --seed para resetar completamente.');
            return;
        }

        $this->command->info('🚀 Criando vagas de estacionamento...');

        $this->createRegularCarSpots();
        $this->createMotorcycleSpots();
        $this->createVipSpots();
        $this->createDisabledSpots();
        $this->setRandomStatuses();

        $total = ParkingSpot::count();
        $available = ParkingSpot::where('status', 'available')->count();

        $this->command->info('');
        $this->command->info('✅ Vagas criadas com sucesso!');
        $this->command->table(
            ['Tipo', 'Quantidade', 'Preço/hora'],
            [
                ['Regular (Carros)', '20', 'R$ 5,00 - R$ 8,00'],
                ['Motos', '15', 'R$ 3,00'],
                ['VIP/Caminhões', '5', 'R$ 12,00'],
                ['Deficientes', '3', 'R$ 4,00'],
                ['TOTAL', $total, '-'],
                ['Disponíveis', $available, '-'],
            ]
        );
    }

    /**
     * Criar vagas regulares para carros (Seção A)
     */
    private function createRegularCarSpots(): void
    {
        $prices = [5.00, 6.00, 7.00, 8.00];

        for ($i = 1; $i <= 20; $i++) {
            ParkingSpot::create([
                'number' => 'A-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'type' => 'regular',
                'status' => 'available',
                'hourly_price' => $prices[array_rand($prices)],
                'width' => 2.50,
                'length' => 5.00,
            ]);
        }
    }

    /**
     * Criar vagas para motos (Seção M) - menores e mais baratas
     */
    private function createMotorcycleSpots(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            ParkingSpot::create([
                'number' => 'M-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'type' => 'regular',
                'status' => 'available',
                'hourly_price' => 3.00,
                'width' => 1.20,
                'length' => 2.50,
            ]);
        }
    }

    /**
     * Criar vagas VIP (maiores e mais caras) para caminhões/SUVs
     */
    private function createVipSpots(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            ParkingSpot::create([
                'number' => 'V-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'type' => 'vip',
                'status' => 'available',
                'hourly_price' => 12.00,
                'width' => 3.50,
                'length' => 10.00,
            ]);
        }
    }

    /**
     * Criar vagas para deficientes (próximas à entrada)
     */
    private function createDisabledSpots(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            ParkingSpot::create([
                'number' => 'D-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'type' => 'disabled',
                'status' => 'available',
                'hourly_price' => 4.00,
                'width' => 3.00,
                'length' => 5.50,
            ]);
        }
    }

    /**
     * Definir alguns status aleatórios para realismo
     */
    private function setRandomStatuses(): void
    {
        ParkingSpot::where('type', 'regular')
            ->where('number', 'LIKE', 'A-%')
            ->inRandomOrder()
            ->limit(4)
            ->update(['status' => 'occupied']);

        ParkingSpot::where('type', 'regular')
            ->where('number', 'LIKE', 'A-%')
            ->where('status', 'available')
            ->inRandomOrder()
            ->limit(3)
            ->update(['status' => 'reserved']);

        ParkingSpot::where('type', 'regular')
            ->where('number', 'LIKE', 'A-%')
            ->where('status', 'available')
            ->inRandomOrder()
            ->limit(2)
            ->update(['status' => 'maintenance']);

        ParkingSpot::where('number', 'LIKE', 'M-%')
            ->inRandomOrder()
            ->limit(2)
            ->update(['status' => 'occupied']);

        ParkingSpot::where('type', 'vip')
            ->inRandomOrder()
            ->limit(1)
            ->update(['status' => 'occupied']);
    }
}
