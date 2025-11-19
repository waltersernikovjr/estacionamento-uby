<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Models\Customer;
use App\Infrastructure\Persistence\Models\ParkingSpot;
use App\Infrastructure\Persistence\Models\Reservation;
use App\Infrastructure\Persistence\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'vehicle_id' => Vehicle::factory(),
            'parking_spot_id' => ParkingSpot::factory(),
            'entry_time' => now(),
            'exit_time' => null,
            'total_amount' => null,
            'status' => 'active',
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'exit_time' => now()->addHours(2),
            'total_amount' => 10.00,
            'status' => 'completed',
        ]);
    }
}
