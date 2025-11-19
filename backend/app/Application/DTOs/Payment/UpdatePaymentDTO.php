<?php

declare(strict_types=1);

namespace App\Application\DTOs\Payment;

use Carbon\Carbon;

final readonly class UpdatePaymentDTO
{
    public function __construct(
        public ?string $payment_method = null,
        public ?string $status = null,
        public ?Carbon $paid_at = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'paid_at' => $this->paid_at?->toDateTimeString(),
        ], fn($value) => $value !== null);
    }
}
