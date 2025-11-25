<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\DTOs\Reservation\CreateReservationDTO;
use App\Application\DTOs\Reservation\CompleteReservationDTO;
use App\Domain\Contracts\Repositories\ReservationRepositoryInterface;
use App\Infrastructure\Persistence\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class ReservationService
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservationRepository,
        private readonly ParkingSpotService $parkingSpotService,
    ) {
    }

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->reservationRepository->paginate($perPage);
    }

    public function listAll(): Collection
    {
        return Reservation::with(['customer', 'vehicle', 'parkingSpot'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findById(int $id): ?Reservation
    {
        return $this->reservationRepository->findById($id);
    }

    public function findActiveBySpot(int $parkingSpotId): ?Reservation
    {
        return $this->reservationRepository->findActiveBySpot($parkingSpotId);
    }

    public function findByCustomer(int $customerId): Collection
    {
        return $this->reservationRepository->getByCustomer($customerId);
    }

    public function hasActiveReservationForVehicle(int $vehicleId): bool
    {
        return Reservation::where('vehicle_id', $vehicleId)
            ->where('status', 'active')
            ->exists();
    }

    public function create(CreateReservationDTO $dto): Reservation
    {
        $parkingSpot = $this->parkingSpotService->findById($dto->parking_spot_id);

        if (!$parkingSpot) {
            throw new \InvalidArgumentException('Parking spot not found');
        }

        if ($parkingSpot->status !== 'available') {
            throw new \InvalidArgumentException('Parking spot is not available');
        }

        if ($this->findActiveBySpot($dto->parking_spot_id)) {
            throw new \InvalidArgumentException('Parking spot already has an active reservation');
        }

        $reservation = $this->reservationRepository->create($dto->toArray());
        $this->parkingSpotService->markAsOccupied($dto->parking_spot_id);

        return $reservation;
    }

    public function complete(int $id, Carbon $exitTime): Reservation
    {
        $reservation = $this->findById($id);

        if (!$reservation) {
            throw new \InvalidArgumentException('Reservation not found');
        }

        if ($reservation->status !== 'active') {
            throw new \InvalidArgumentException('Only active reservations can be completed');
        }

        $parkingSpot = $this->parkingSpotService->findById($reservation->parking_spot_id);
        $hourlyPrice = $parkingSpot ? (float) $parkingSpot->hourly_price : 5.0;

        $totalAmount = $this->calculateAmount($reservation->entry_time, $exitTime, $hourlyPrice);

        $dto = new CompleteReservationDTO(
            exit_time: $exitTime,
            total_amount: $totalAmount,
        );

        $completedReservation = $this->reservationRepository->complete($id, $dto->toArray());

        if (!$completedReservation) {
            throw new \InvalidArgumentException('Failed to complete reservation');
        }

        $this->parkingSpotService->markAsAvailable($reservation->parking_spot_id);

        return $completedReservation;
    }

    public function cancel(int $id): Reservation
    {
        $reservation = $this->findById($id);

        if (!$reservation) {
            throw new \InvalidArgumentException('Reservation not found');
        }

        if ($reservation->status !== 'active') {
            throw new \InvalidArgumentException('Only active reservations can be cancelled');
        }

        $this->reservationRepository->update($id, [
            'status' => 'cancelled',
            'exit_time' => now(),
        ]);

        $this->parkingSpotService->markAsAvailable($reservation->parking_spot_id);

        return $this->findById($id);
    }

    private function calculateAmount(Carbon $entryTime, Carbon $exitTime, float $hourlyPrice = 5.0): float
    {
        $hours = $entryTime->diffInHours($exitTime);
        $minutes = $entryTime->copy()->addHours($hours)->diffInMinutes($exitTime);

        $amount = $hours * $hourlyPrice;

        if ($minutes > 0) {
            $amount += ($minutes / 60) * $hourlyPrice;
        }

        return round($amount, 2);
    }
}
