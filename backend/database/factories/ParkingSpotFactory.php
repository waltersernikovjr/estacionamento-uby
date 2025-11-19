<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Models\ParkingSpot;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParkingSpotFactory extends Factory
{
    protected $model = ParkingSpot::class;

    public function definition(): array
    {
        return [
            'number' => fake()->bothify('?##'),
            'type' => fake()->randomElement(['regular', 'vip', 'disabled']),
            'hourly_price' => 5.00,
            'width' => 2.50,
            'length' => 5.00,
            'status' => 'available',
            'operator_id' => null,
        ];
    }

    public function occupied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'occupied',
        ]);
    }
}
