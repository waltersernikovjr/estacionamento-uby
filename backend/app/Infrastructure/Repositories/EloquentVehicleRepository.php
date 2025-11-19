<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\Repositories\VehicleRepositoryInterface;
use App\Infrastructure\Persistence\Models\Vehicle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class EloquentVehicleRepository implements VehicleRepositoryInterface
{
    public function findById(int $id): ?Vehicle
    {
        return Vehicle::find($id);
    }

    public function create(array $data): Vehicle
    {
        return Vehicle::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Vehicle::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Vehicle::destroy($id) > 0;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Vehicle::with('customer')->paginate($perPage);
    }

    public function findByCustomer(int $customerId): Collection
    {
        return Vehicle::where('customer_id', $customerId)->get();
    }

    public function findByLicensePlate(string $licensePlate): ?Vehicle
    {
        return Vehicle::where('license_plate', $licensePlate)->first();
    }

    public function licensePlateExists(string $licensePlate): bool
    {
        return Vehicle::where('license_plate', $licensePlate)->exists();
    }
}
