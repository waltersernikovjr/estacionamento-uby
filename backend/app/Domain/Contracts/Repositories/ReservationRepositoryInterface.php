<?php

namespace App\Domain\Contracts\Repositories;

use App\Infrastructure\Persistence\Models\Reservation;
use Illuminate\Database\Eloquent\Collection;

interface ReservationRepositoryInterface
{
    /**
     * Find a reservation by ID.
     */
    public function findById(int $id): ?Reservation;

    /**
     * Get all reservations.
     */
    public function all(): Collection;

    /**
     * Get active reservations.
     */
    public function getActive(): Collection;

    /**
     * Get reservations by customer.
     */
    public function getByCustomer(int $customerId): Collection;

    /**
     * Get active reservation by parking spot.
     */
    public function findActiveBySpot(int $spotId): ?Reservation;

    /**
     * Create a new reservation.
     */
    public function create(array $data): Reservation;

    /**
     * Update a reservation.
     */
    public function update(int $id, array $data): bool;

    /**
     * Update reservation status.
     */
    public function updateStatus(int $id, string $status): bool;

    /**
     * Complete a reservation (set exit_time and total_amount).
     */
    public function complete(int $id, array $data): ?Reservation;

    /**
     * Find reservation with relationships.
     */
    public function findWithRelations(int $id): ?Reservation;
}
