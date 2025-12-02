<?php

namespace App\Domain\Contracts\Repositories;

use App\Infrastructure\Persistence\Models\ParkingSpot;
use Illuminate\Database\Eloquent\Collection;

interface ParkingSpotRepositoryInterface
{
    /**
     * Find a parking spot by ID.
     */
    public function findById(int $id): ?ParkingSpot;

    /**
     * Find a parking spot by number.
     */
    public function findByNumber(string $number): ?ParkingSpot;

    /**
     * Get all parking spots.
     */
    public function all(): Collection;

    /**
     * Get available parking spots.
     */
    public function getAvailable(): Collection;

    /**
     * Get parking spots by operator.
     */
    public function getByOperator(int $operatorId): Collection;

    /**
     * Create a new parking spot.
     */
    public function create(array $data): ParkingSpot;

    /**
     * Update a parking spot.
     */
    public function update(int $id, array $data): ?ParkingSpot;

    /**
     * Delete a parking spot.
     */
    public function delete(int $id): bool;

    /**
     * Update parking spot status.
     */
    public function updateStatus(int $id, string $status): bool;

    /**
     * Check if number exists.
     */
    public function numberExists(string $number, ?int $excludeId = null): bool;

    /**
     * Find available spot by ID.
     */
    public function findAvailable(int $id): ?ParkingSpot;
}
