<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Application\DTOs\CreateReservationDTO;
use App\Application\DTOs\UpdateReservationDTO;
use App\Application\Services\ReservationService;
use App\Domain\Entities\Customer;
use App\Domain\Entities\ParkingSpot;
use App\Domain\Entities\Reservation;
use App\Domain\Entities\Vehicle;
use App\Domain\Repositories\ParkingSpotRepositoryInterface;
use App\Domain\Repositories\ReservationRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Mockery;

class ReservationServiceTest extends TestCase
{
    private ReservationService $service;
    private ReservationRepositoryInterface $repository;
    private ParkingSpotRepositoryInterface $parkingSpotRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(ReservationRepositoryInterface::class);
        $this->parkingSpotRepository = Mockery::mock(ParkingSpotRepositoryInterface::class);
        $this->service = new ReservationService($this->repository, $this->parkingSpotRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_reservation_successfully(): void
    {
        $dto = new CreateReservationDTO(
            customerId: 1,
            vehicleId: 1,
            parkingSpotId: 1,
            entryTime: Carbon::now(),
            expectedExitTime: Carbon::now()->addHours(2)
        );

        $parkingSpot = Mockery::mock(ParkingSpot::class);
        $parkingSpot->shouldReceive('getAttribute')
            ->with('status')
            ->andReturn('available');

        $reservation = new Reservation();
        $reservation->id = 1;
        $reservation->customer_id = 1;
        $reservation->vehicle_id = 1;
        $reservation->parking_spot_id = 1;
        $reservation->status = 'active';

        $this->parkingSpotRepository->shouldReceive('find')
            ->with(1)
            ->andReturn($parkingSpot);

        $this->repository->shouldReceive('hasActiveReservation')
            ->with(1)
            ->andReturn(false);

        $this->repository->shouldReceive('create')
            ->with($dto)
            ->andReturn($reservation);

        $result = $this->service->create($dto);

        $this->assertInstanceOf(Reservation::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('active', $result->status);
    }

    public function test_create_reservation_fails_when_spot_is_occupied(): void
    {
        $dto = new CreateReservationDTO(
            customerId: 1,
            vehicleId: 1,
            parkingSpotId: 1,
            entryTime: Carbon::now(),
            expectedExitTime: Carbon::now()->addHours(2)
        );

        $parkingSpot = Mockery::mock(ParkingSpot::class);
        $parkingSpot->shouldReceive('getAttribute')
            ->with('status')
            ->andReturn('occupied');

        $this->parkingSpotRepository->shouldReceive('find')
            ->with(1)
            ->andReturn($parkingSpot);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Vaga não está disponível');

        $this->service->create($dto);
    }

    public function test_create_reservation_fails_when_spot_has_active_reservation(): void
    {
        $dto = new CreateReservationDTO(
            customerId: 1,
            vehicleId: 1,
            parkingSpotId: 1,
            entryTime: Carbon::now(),
            expectedExitTime: Carbon::now()->addHours(2)
        );

        $parkingSpot = Mockery::mock(ParkingSpot::class);
        $parkingSpot->shouldReceive('getAttribute')
            ->with('status')
            ->andReturn('available');

        $this->parkingSpotRepository->shouldReceive('find')
            ->with(1)
            ->andReturn($parkingSpot);

        $this->repository->shouldReceive('hasActiveReservation')
            ->with(1)
            ->andReturn(true);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('já possui uma reserva ativa');

        $this->service->create($dto);
    }

    public function test_complete_reservation_successfully(): void
    {
        $reservation = new Reservation();
        $reservation->id = 1;
        $reservation->status = 'active';
        $reservation->entry_time = Carbon::now()->subHours(2);

        $exitTime = Carbon::now();

        $this->repository->shouldReceive('find')
            ->with(1)
            ->andReturn($reservation);

        $this->repository->shouldReceive('update')
            ->once()
            ->andReturn($reservation);

        $result = $this->service->complete(1, $exitTime);

        $this->assertInstanceOf(Reservation::class, $result);
        $this->assertEquals('completed', $result->status);
        $this->assertNotNull($result->exit_time);
        $this->assertNotNull($result->total_amount);
        $this->assertGreaterThan(0, $result->total_amount);
    }

    public function test_complete_reservation_fails_when_not_active(): void
    {
        $reservation = new Reservation();
        $reservation->id = 1;
        $reservation->status = 'completed';

        $this->repository->shouldReceive('find')
            ->with(1)
            ->andReturn($reservation);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Apenas reservas ativas podem ser finalizadas');

        $this->service->complete(1, Carbon::now());
    }

    public function test_cancel_reservation_successfully(): void
    {
        $reservation = new Reservation();
        $reservation->id = 1;
        $reservation->status = 'active';

        $this->repository->shouldReceive('find')
            ->with(1)
            ->andReturn($reservation);

        $this->repository->shouldReceive('update')
            ->once()
            ->andReturn($reservation);

        $result = $this->service->cancel(1);

        $this->assertInstanceOf(Reservation::class, $result);
        $this->assertEquals('cancelled', $result->status);
    }

    public function test_cancel_reservation_fails_when_not_active(): void
    {
        $reservation = new Reservation();
        $reservation->id = 1;
        $reservation->status = 'completed';

        $this->repository->shouldReceive('find')
            ->with(1)
            ->andReturn($reservation);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Apenas reservas ativas podem ser canceladas');

        $this->service->cancel(1);
    }

    public function test_calculate_parking_amount_correctly(): void
    {
        $entryTime = Carbon::parse('2025-01-18 14:00:00');
        $exitTime = Carbon::parse('2025-01-18 16:30:00'); // 2h30min = 12.50

        $amount = $this->service->calculateAmount($entryTime, $exitTime);

        $this->assertEquals(12.50, $amount);
    }

    public function test_calculate_parking_amount_for_exact_hours(): void
    {
        $entryTime = Carbon::parse('2025-01-18 14:00:00');
        $exitTime = Carbon::parse('2025-01-18 17:00:00'); // 3h = 15.00

        $amount = $this->service->calculateAmount($entryTime, $exitTime);

        $this->assertEquals(15.00, $amount);
    }

    public function test_calculate_parking_amount_for_less_than_hour(): void
    {
        $entryTime = Carbon::parse('2025-01-18 14:00:00');
        $exitTime = Carbon::parse('2025-01-18 14:45:00'); // 45min = 5.00

        $amount = $this->service->calculateAmount($entryTime, $exitTime);

        $this->assertEquals(5.00, $amount);
    }

    public function test_find_by_customer_returns_collection(): void
    {
        $reservations = new Collection([
            new Reservation(),
            new Reservation()
        ]);

        $this->repository->shouldReceive('findByCustomer')
            ->with(1)
            ->andReturn($reservations);

        $result = $this->service->findByCustomer(1);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }
}
