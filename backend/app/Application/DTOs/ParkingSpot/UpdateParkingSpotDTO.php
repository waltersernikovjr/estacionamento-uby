<?php

declare(strict_types=1);

namespace App\Application\DTOs\ParkingSpot;

final readonly class UpdateParkingSpotDTO
{
    public function __construct(
        public ?string $number = null,
        public ?string $type = null,
        public ?string $status = null,
        public ?float $hourly_price = null,
        public ?float $width = null,
        public ?float $length = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'number' => $this->number,
            'type' => $this->type,
            'status' => $this->status,
            'hourly_price' => $this->hourly_price,
            'width' => $this->width,
            'length' => $this->length,
        ], fn($value) => $value !== null);
    }
}
