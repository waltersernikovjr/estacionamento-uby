<?php

declare(strict_types=1);

namespace App\Application\DTOs\ParkingSpot;

final readonly class CreateParkingSpotDTO
{
    public function __construct(
        public string $number,
        public string $type,
        public string $status = 'available',
    ) {
    }

    public function toArray(): array
    {
        return [
            'number' => $this->number,
            'type' => $this->type,
            'status' => $this->status,
        ];
    }
}
