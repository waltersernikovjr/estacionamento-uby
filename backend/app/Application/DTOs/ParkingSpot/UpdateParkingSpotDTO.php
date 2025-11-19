<?php

declare(strict_types=1);

namespace App\Application\DTOs\ParkingSpot;

final readonly class UpdateParkingSpotDTO
{
    public function __construct(
        public ?string $number = null,
        public ?string $type = null,
        public ?string $status = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'number' => $this->number,
            'type' => $this->type,
            'status' => $this->status,
        ], fn($value) => $value !== null);
    }
}
