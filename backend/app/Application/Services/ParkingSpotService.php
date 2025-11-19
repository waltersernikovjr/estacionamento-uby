<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\DTOs\ParkingSpot\CreateParkingSpotDTO;
use App\Application\DTOs\ParkingSpot\UpdateParkingSpotDTO;
use App\Domain\Contracts\Repositories\ParkingSpotRepositoryInterface;
use App\Infrastructure\Persistence\Models\ParkingSpot;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class ParkingSpotService
{
    public function __construct(
        private readonly ParkingSpotRepositoryInterface $parkingSpotRepository
    ) {
    }

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->parkingSpotRepository->paginate($perPage);
    }

    public function getAvailable(): Collection
    {
        return $this->parkingSpotRepository->getAvailable();
    }

    public function findAvailable(string $type): ?ParkingSpot
    {
        return $this->parkingSpotRepository->findAvailable($type);
    }

    public function findById(int $id): ?ParkingSpot
    {
        return $this->parkingSpotRepository->findById($id);
    }

    public function create(CreateParkingSpotDTO $dto): ParkingSpot
    {
        if ($this->parkingSpotRepository->numberExists($dto->number)) {
            throw new \InvalidArgumentException('Parking spot number already exists');
        }

        return $this->parkingSpotRepository->create($dto->toArray());
    }

    public function update(int $id, UpdateParkingSpotDTO $dto): ParkingSpot
    {
        $parkingSpot = $this->findById($id);
        
        if (!$parkingSpot) {
            throw new \InvalidArgumentException('Parking spot not found');
        }

        if ($dto->number && $dto->number !== $parkingSpot->number) {
            if ($this->parkingSpotRepository->numberExists($dto->number)) {
                throw new \InvalidArgumentException('Parking spot number already exists');
            }
        }

        $updated = $this->parkingSpotRepository->update($id, $dto->toArray());
        
        if (!$updated) {
            throw new \InvalidArgumentException('Failed to update parking spot');
        }
        
        return $updated;
    }

    public function delete(int $id): bool
    {
        $parkingSpot = $this->findById($id);
        
        if (!$parkingSpot) {
            throw new \InvalidArgumentException('Parking spot not found');
        }

        if ($parkingSpot->status === 'occupied') {
            throw new \InvalidArgumentException('Cannot delete occupied parking spot');
        }

        return $this->parkingSpotRepository->delete($id);
    }

    public function markAsOccupied(int $id): ParkingSpot
    {
        $updated = $this->parkingSpotRepository->update($id, ['status' => 'occupied']);
        
        if (!$updated) {
            throw new \InvalidArgumentException('Failed to mark parking spot as occupied');
        }
        
        return $updated;
    }

    public function markAsAvailable(int $id): ParkingSpot
    {
        $updated = $this->parkingSpotRepository->update($id, ['status' => 'available']);
        
        if (!$updated) {
            throw new \InvalidArgumentException('Failed to mark parking spot as available');
        }
        
        return $updated;
    }
}
