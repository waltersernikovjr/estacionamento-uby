<?php

declare(strict_types=1);

namespace App\Application\DTOs\Vehicle;

final readonly class UpdateVehicleDTO
{
    public function __construct(
        public ?string $license_plate = null,
        public ?string $brand = null,
        public ?string $model = null,
        public ?string $color = null,
        public ?string $type = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'license_plate' => $this->license_plate,
            'brand' => $this->brand,
            'model' => $this->model,
            'color' => $this->color,
            'type' => $this->type,
        ], fn($value) => $value !== null);
    }
}
