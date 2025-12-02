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
            'address_zipcode' => fake()->numerify('########'),
            'address_street' => fake()->streetName(),
            'address_number' => fake()->buildingNumber(),
            'address_complement' => fake()->optional()->secondaryAddress(),
            'address_neighborhood' => fake()->citySuffix(),
            'address_city' => fake()->city(),
            'address_state' => fake()->stateAbbr(),
        ];
    }
}
