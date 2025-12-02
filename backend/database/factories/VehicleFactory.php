<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Models\Customer;
use App\Infrastructure\Persistence\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'license_plate' => fake()->bothify('???####'),
            'brand' => fake()->randomElement(['Toyota', 'Honda', 'Ford', 'Chevrolet', 'Volkswagen']),
            'model' => fake()->randomElement(['Corolla', 'Civic', 'Focus', 'Onix', 'Gol']),
            'color' => fake()->safeColorName(),
            'type' => fake()->randomElement(['car', 'motorcycle', 'truck', 'van']),
            'is_active' => true,
        ];
    }
}
