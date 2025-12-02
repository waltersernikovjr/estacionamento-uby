<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\Repositories\ReservationRepositoryInterface;
use App\Infrastructure\Persistence\Models\Reservation;
use Illuminate\Database\Eloquent\Collection;

final class EloquentReservationRepository implements ReservationRepositoryInterface
{
    public function findById(int $id): ?Reservation
    {
        return Reservation::find($id);
    }

    public function all(): Collection
    {
        return Reservation::with(['customer', 'vehicle', 'parkingSpot'])->get();
    }

    public function getActive(): Collection
    {
        return Reservation::with(['customer', 'vehicle', 'parkingSpot'])
            ->where('status', 'active')
            ->get();
    }

    public function getByCustomer(int $customerId): Collection
    {
        return Reservation::with(['vehicle', 'parkingSpot.operator', 'payment'])
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findActiveBySpot(int $spotId): ?Reservation
    {
        return Reservation::where('parking_spot_id', $spotId)
            ->where('status', 'active')
            ->first();
    }

    public function create(array $data): Reservation
    {
        return Reservation::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Reservation::where('id', $id)->update($data);
    }

    public function updateStatus(int $id, string $status): bool
    {
        return Reservation::where('id', $id)->update(['status' => $status]);
    }

    public function complete(int $id, array $data): ?Reservation
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return null;
        }

        $reservation->update(array_merge($data, ['status' => 'completed']));

        return $reservation->fresh();
    }

    public function findWithRelations(int $id): ?Reservation
    {
        return Reservation::with(['customer', 'vehicle', 'parkingSpot', 'payment'])->find($id);
    }
}
