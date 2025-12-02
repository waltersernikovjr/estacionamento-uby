<?php

declare(strict_types=1);

namespace App\Application\DTOs\Reservation;

use Carbon\Carbon;

final readonly class CompleteReservationDTO
{
    public function __construct(
        public Carbon $exit_time,
        public float $total_amount,
    ) {
    }

    public function toArray(): array
    {
        return [
            'exit_time' => $this->exit_time->toDateTimeString(),
            'total_amount' => $this->total_amount,
            'status' => 'completed',
        ];
    }
}
