<?php

namespace Tests\Unit\Services;

use App\Application\DTOs\Reservation\CreateReservationDTO;
use App\Application\Services\ParkingSpotService;
use App\Application\Services\ReservationService;
use App\Domain\Contracts\Repositories\PaymentRepositoryInterface;
use App\Domain\Contracts\Repositories\ReservationRepositoryInterface;
use App\Infrastructure\Persistence\Models\ParkingSpot;
use App\Infrastructure\Persistence\Models\Reservation;
use Carbon\Carbon;
use Tests\TestCase;

class ReservationServiceTest extends TestCase
{
    private ReservationRepositoryInterface $repository;
    private ParkingSpotService $parkingSpotService;
    private PaymentRepositoryInterface $paymentRepository;
    private ReservationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(ReservationRepositoryInterface::class);
        $this->parkingSpotService = $this->createMock(ParkingSpotService::class);
        $this->paymentRepository = $this->createMock(PaymentRepositoryInterface::class);
        $this->service = new ReservationService(
            $this->repository,
            $this->parkingSpotService,
            $this->paymentRepository
        );
    }

    public function test_creates_reservation_when_parking_spot_is_available(): void
    {
        $dto = new CreateReservationDTO(
            customer_id: 1,
            vehicle_id: 1,
            parking_spot_id: 1,
            entry_time: Carbon::now(),
            expected_exit_time: Carbon::now()->addHours(2)
        );

        $parkingSpot = new ParkingSpot();
        $parkingSpot->id = 1;
        $parkingSpot->status = 'available';

        $this->parkingSpotService
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($parkingSpot);

        $this->repository
            ->expects($this->once())
            ->method('findActiveBySpot')
            ->with(1)
            ->willReturn(null);

        $reservation = new Reservation();
        $reservation->id = 1;
        $reservation->status = 'active';

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with($dto->toArray())
            ->willReturn($reservation);

        $this->parkingSpotService
            ->expects($this->once())
            ->method('markAsOccupied')
            ->with(1);

        $result = $this->service->create($dto);

        $this->assertInstanceOf(Reservation::class, $result);
        $this->assertEquals('active', $result->status);
    }

    public function test_throws_exception_when_parking_spot_not_found(): void
    {
        $dto = new CreateReservationDTO(
            customer_id: 1,
            vehicle_id: 1,
            parking_spot_id: 999,
            entry_time: Carbon::now(),
            expected_exit_time: null
        );

        $this->parkingSpotService
            ->expects($this->once())
            ->method('findById')
            ->with(999)
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Parking spot not found');

        $this->service->create($dto);
    }

    public function test_throws_exception_when_parking_spot_not_available(): void
    {
        $dto = new CreateReservationDTO(
            customer_id: 1,
            vehicle_id: 1,
            parking_spot_id: 1,
            entry_time: Carbon::now(),
            expected_exit_time: null
        );

        $parkingSpot = new ParkingSpot();
        $parkingSpot->id = 1;
        $parkingSpot->status = 'occupied';

        $this->parkingSpotService
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($parkingSpot);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Parking spot is not available');

        $this->service->create($dto);
    }

    public function test_throws_exception_when_parking_spot_has_active_reservation(): void
    {
        $dto = new CreateReservationDTO(
            customer_id: 1,
            vehicle_id: 1,
            parking_spot_id: 1,
            entry_time: Carbon::now(),
            expected_exit_time: null
        );

        $parkingSpot = new ParkingSpot();
        $parkingSpot->id = 1;
        $parkingSpot->status = 'available';

        $activeReservation = new Reservation();
        $activeReservation->id = 1;
        $activeReservation->status = 'active';

        $this->parkingSpotService
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($parkingSpot);

        $this->repository
            ->expects($this->once())
            ->method('findActiveBySpot')
            ->with(1)
            ->willReturn($activeReservation);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Parking spot already has an active reservation');

        $this->service->create($dto);
    }

    public function test_completes_reservation_and_calculates_correct_amount(): void
    {
        $entryTime = Carbon::parse('2025-01-01 10:00:00');
        $exitTime = Carbon::parse('2025-01-01 12:00:00');

        $reservation = $this->createMock(Reservation::class);
        $reservation->method('__get')->willReturnCallback(function ($name) use ($entryTime) {
            return match($name) {
                'id' => 1,
                'status' => 'active',
                'entry_time' => $entryTime,
                'parking_spot_id' => 1,
                default => null
            };
        });

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($reservation);

        $completedReservation = $this->createMock(Reservation::class);
        $completedReservation->method('__get')->willReturnCallback(function ($name) {
            return match($name) {
                'id' => 1,
                'status' => 'completed',
                'total_amount' => 10.0,
                default => null
            };
        });

        $this->repository
            ->expects($this->once())
            ->method('complete')
            ->with(1, $this->callback(function ($data) use ($exitTime) {
                return isset($data['exit_time'])
                    && isset($data['total_amount'])
                    && $data['total_amount'] === 10.0;
            }))
            ->willReturn($completedReservation);

        $this->paymentRepository
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($data) {
                return $data['reservation_id'] === 1
                    && $data['amount'] === 10.0
                    && $data['status'] === 'paid';
            }));

        $this->parkingSpotService
            ->expects($this->once())
            ->method('markAsAvailable')
            ->with(1);

        $result = $this->service->complete(1, $exitTime);

        $this->assertEquals('completed', $result->status);
        $this->assertEquals(10.0, $result->total_amount);
    }

    public function test_throws_exception_when_completing_non_existent_reservation(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with(999)
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Reservation not found');

        $this->service->complete(999, Carbon::now());
    }

    public function test_throws_exception_when_completing_non_active_reservation(): void
    {
        $reservation = new Reservation();
        $reservation->id = 1;
        $reservation->status = 'completed';

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($reservation);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Only active reservations can be completed');

        $this->service->complete(1, Carbon::now());
    }

    public function test_cancels_active_reservation(): void
    {
        $reservation = new Reservation();
        $reservation->id = 1;
        $reservation->status = 'active';
        $reservation->parking_spot_id = 1;

        $cancelledReservation = new Reservation();
        $cancelledReservation->id = 1;
        $cancelledReservation->status = 'cancelled';

        $this->repository
            ->expects($this->exactly(2))
            ->method('findById')
            ->with(1)
            ->willReturnOnConsecutiveCalls($reservation, $cancelledReservation);

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with(1, $this->callback(function ($data) {
                return $data['status'] === 'cancelled'
                    && isset($data['exit_time']);
            }));

        $this->parkingSpotService
            ->expects($this->once())
            ->method('markAsAvailable')
            ->with(1);

        $result = $this->service->cancel(1);

        $this->assertEquals('cancelled', $result->status);
    }

    public function test_throws_exception_when_cancelling_non_active_reservation(): void
    {
        $reservation = new Reservation();
        $reservation->id = 1;
        $reservation->status = 'completed';

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($reservation);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Only active reservations can be cancelled');

        $this->service->cancel(1);
    }
}
