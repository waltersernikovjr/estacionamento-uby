<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\DTOs\Reservation\CreateReservationDTO;
use App\Application\DTOs\Reservation\CompleteReservationDTO;
use App\Domain\Contracts\Repositories\ReservationRepositoryInterface;
use App\Infrastructure\Persistence\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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

    public function findById(int $id): ?Reservation
    {
        return $this->reservationRepository->findById($id);
    }

    public function findActiveBySpot(int $parkingSpotId): ?Reservation
    {
        return $this->reservationRepository->findActiveBySpot($parkingSpotId);
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

        $totalAmount = $this->calculateAmount($reservation->entry_time, $exitTime);

        $dto = new CompleteReservationDTO(
            exit_time: $exitTime,
            total_amount: $totalAmount,
        );

        $completedReservation = $this->reservationRepository->complete($id, $dto->toArray());
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

        $cancelledReservation = $this->reservationRepository->update($id, [
            'status' => 'cancelled',
            'exit_time' => now(),
        ]);

        $this->parkingSpotService->markAsAvailable($reservation->parking_spot_id);

        return $cancelledReservation;
    }

    private function calculateAmount(Carbon $entryTime, Carbon $exitTime): float
    {
        $hours = $entryTime->diffInHours($exitTime);
        $minutes = $entryTime->copy()->addHours($hours)->diffInMinutes($exitTime);

        $amount = $hours * 5.0;
        
        if ($minutes > 0) {
            $additionalBlocks = ceil($minutes / 15);
            $amount += $additionalBlocks * 1.0;
        }

        return round($amount, 2);
    }
}
