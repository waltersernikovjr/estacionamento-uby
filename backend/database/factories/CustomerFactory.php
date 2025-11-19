<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = \App\Infrastructure\Persistence\Models\Customer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->numerify('###########'),
            'phone' => fake()->phoneNumber(),
            'password' => bcrypt('password'),
            'zip_code' => fake()->postcode(),
            'street' => fake()->streetName(),
            'number' => fake()->buildingNumber(),
            'complement' => fake()->optional()->secondaryAddress(),
            'neighborhood' => fake()->citySuffix(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
        ];
    }
}
