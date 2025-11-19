<?php

declare(strict_types=1);

namespace App\Application\DTOs\Vehicle;

final readonly class CreateVehicleDTO
{
    public function __construct(
        public int $customer_id,
        public string $license_plate,
        public string $brand,
        public string $model,
        public string $color,
        public string $type,
    ) {
    }

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customer_id,
            'license_plate' => $this->license_plate,
            'brand' => $this->brand,
            'model' => $this->model,
            'color' => $this->color,
            'type' => $this->type,
        ];
    }
}
