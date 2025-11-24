<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'cpf' => fake()->unique()->numerify('###########'),
            'rg' => fake()->unique()->numerify('#########'),
            'endereco' => fake()->streetAddress() . ', ' . fake()->city(),
            'password' => bcrypt('password'),
        ];
    }
}
