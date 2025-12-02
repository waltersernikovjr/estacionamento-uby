<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\Repositories\ParkingSpotRepositoryInterface;
use App\Infrastructure\Persistence\Models\ParkingSpot;
use Illuminate\Database\Eloquent\Collection;

final class EloquentParkingSpotRepository implements ParkingSpotRepositoryInterface
{
    public function findById(int $id): ?ParkingSpot
    {
        return ParkingSpot::find($id);
    }

    public function findByNumber(string $number): ?ParkingSpot
    {
        return ParkingSpot::where('number', $number)->first();
    }

    public function all(): Collection
    {
        return ParkingSpot::all();
    }

    public function getAvailable(): Collection
    {
        return ParkingSpot::where('status', 'available')->get();
    }

    public function getByOperator(int $operatorId): Collection
    {
        return ParkingSpot::where('operator_id', $operatorId)->get();
    }

    public function create(array $data): ParkingSpot
    {
        return ParkingSpot::create($data);
    }

    public function update(int $id, array $data): ?ParkingSpot
    {
        $parkingSpot = ParkingSpot::find($id);
        
        if (!$parkingSpot) {
            return null;
        }
        
        $parkingSpot->update($data);
        
        return $parkingSpot->fresh();
    }

    public function delete(int $id): bool
    {
        return ParkingSpot::where('id', $id)->delete();
    }

    public function updateStatus(int $id, string $status): bool
    {
        return ParkingSpot::where('id', $id)->update(['status' => $status]);
    }

    public function numberExists(string $number, ?int $excludeId = null): bool
    {
        $query = ParkingSpot::where('number', $number);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    public function findAvailable(int $id): ?ParkingSpot
    {
        return ParkingSpot::where('id', $id)
            ->where('status', 'available')
            ->first();
    }
}
