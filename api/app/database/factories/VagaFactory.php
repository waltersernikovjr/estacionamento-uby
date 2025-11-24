<?php

namespace Database\Factories;

use App\Models\Vaga;
use Illuminate\Database\Eloquent\Factories\Factory;

class VagaFactory extends Factory
{
    protected $model = Vaga::class;

    public function definition(): array
    {
        return [
            'numero' => 'V' . fake()->unique()->numberBetween(1, 200),
            'preco_por_hora' => fake()->randomFloat(2, 5, 20),
            'largura' => fake()->randomFloat(2, 2.2, 3.5),
            'comprimento' => fake()->randomFloat(2, 4.5, 6.0),
            'disponivel' => true,
        ];
    }
}
