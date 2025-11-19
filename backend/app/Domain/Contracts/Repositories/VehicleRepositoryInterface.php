<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories;

use App\Infrastructure\Persistence\Models\Vehicle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface VehicleRepositoryInterface
{
    public function findById(int $id): ?Vehicle;
    
    public function create(array $data): Vehicle;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    
    public function findByCustomer(int $customerId): Collection;
    
    public function findByLicensePlate(string $licensePlate): ?Vehicle;
    
    public function licensePlateExists(string $licensePlate): bool;
}
