<?php

namespace Database\Factories;

use App\Models\Veiculo;
use Illuminate\Database\Eloquent\Factories\Factory;

class VeiculoFactory extends Factory
{
    protected $model = Veiculo::class;

    public function definition(): array
    {
        return [
            'cliente_id' => null, // será definido com ->for(Cliente::factory())
            'placa' => strtoupper(fake()->bothify('???####')),
            'modelo' => fake()->randomElement(['Civic', 'Corolla', 'Onix', 'Gol', 'HB20', 'Ka', 'Uno', 'Palio']),
            'cor' => fake()->randomElement(['Preto', 'Prata', 'Branco', 'Cinza', 'Vermelho', 'Azul']),
            'ano' => fake()->numberBetween(1990, date('Y') + 1),
        ];
    }
}
