<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\DTOs\Vehicle\CreateVehicleDTO;
use App\Application\DTOs\Vehicle\UpdateVehicleDTO;
use App\Domain\Contracts\Repositories\VehicleRepositoryInterface;
use App\Infrastructure\Persistence\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;

final class VehicleService
{
    public function __construct(
        private readonly VehicleRepositoryInterface $vehicleRepository
    ) {
    }

    public function findById(int $id): ?Vehicle
    {
        return $this->vehicleRepository->findById($id);
    }

    public function findByCustomer(int $customerId): Collection
    {
        return $this->vehicleRepository->findByCustomer($customerId);
    }

    public function create(CreateVehicleDTO $dto): Vehicle
    {
        if ($this->vehicleRepository->licensePlateExists($dto->license_plate)) {
            throw new \InvalidArgumentException('License plate already registered');
        }

        return $this->vehicleRepository->create($dto->toArray());
    }

    public function update(int $id, UpdateVehicleDTO $dto): Vehicle
    {
        $vehicle = $this->findById($id);
        
        if (!$vehicle) {
            throw new \InvalidArgumentException('Vehicle not found');
        }

        if ($dto->license_plate && $dto->license_plate !== $vehicle->license_plate) {
            if ($this->vehicleRepository->licensePlateExists($dto->license_plate)) {
                throw new \InvalidArgumentException('License plate already registered');
            }
        }

        $this->vehicleRepository->update($id, $dto->toArray());
        
        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $vehicle = $this->findById($id);
        
        if (!$vehicle) {
            throw new \InvalidArgumentException('Vehicle not found');
        }

        return $this->vehicleRepository->delete($id);
    }
}
