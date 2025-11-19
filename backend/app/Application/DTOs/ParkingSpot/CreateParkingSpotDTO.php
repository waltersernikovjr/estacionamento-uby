<?php

declare(strict_types=1);

namespace App\Application\DTOs\ParkingSpot;

final readonly class CreateParkingSpotDTO
{
    public function __construct(
        public string $number,
        public string $type,
        public float $hourly_price = 5.00,
        public float $width = 2.50,
        public float $length = 5.00,
        public string $status = 'available',
        public ?int $operator_id = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'number' => $this->number,
            'type' => $this->type,
            'hourly_price' => $this->hourly_price,
            'width' => $this->width,
            'length' => $this->length,
            'status' => $this->status,
            'operator_id' => $this->operator_id,
        ];
    }
}
