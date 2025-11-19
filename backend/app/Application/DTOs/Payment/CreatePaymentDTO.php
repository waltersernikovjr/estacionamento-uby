<?php

declare(strict_types=1);

namespace App\Application\DTOs\Payment;

use Carbon\Carbon;

final readonly class CreatePaymentDTO
{
    public function __construct(
        public int $reservation_id,
        public float $amount,
        public string $payment_method,
        public string $status = 'pending',
        public ?Carbon $paid_at = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'reservation_id' => $this->reservation_id,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'paid_at' => $this->paid_at?->toDateTimeString(),
        ];
    }
}
