<?php

declare(strict_types=1);

namespace App\Application\DTOs\Reservation;

use Carbon\Carbon;

final readonly class CreateReservationDTO
{
    public function __construct(
        public int $customer_id,
        public int $vehicle_id,
        public int $parking_spot_id,
        public Carbon $entry_time,
        public ?Carbon $expected_exit_time = null,
        public string $status = 'active',
    ) {
    }

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customer_id,
            'vehicle_id' => $this->vehicle_id,
            'parking_spot_id' => $this->parking_spot_id,
            'entry_time' => $this->entry_time->toDateTimeString(),
            'expected_exit_time' => $this->expected_exit_time?->toDateTimeString(),
            'status' => $this->status,
        ];
    }
}
