<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Models\Payment;
use App\Infrastructure\Persistence\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory()->completed(),
            'amount' => 10.00,
            'payment_method' => fake()->randomElement(['credit_card', 'debit_card', 'pix', 'cash']),
            'status' => 'pending',
            'paid_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }
}
