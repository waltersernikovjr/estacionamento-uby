<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Application\DTOs\ParkingSpot\CreateParkingSpotDTO;
use App\Application\DTOs\ParkingSpot\UpdateParkingSpotDTO;
use App\Application\Services\ParkingSpotService;
use App\Domain\Contracts\Repositories\ParkingSpotRepositoryInterface;
use App\Infrastructure\Persistence\Models\ParkingSpot;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;

final class ParkingSpotServiceTest extends TestCase
{
    private ParkingSpotRepositoryInterface $repository;
    private ParkingSpotService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(ParkingSpotRepositoryInterface::class);
        $this->service = new ParkingSpotService($this->repository);
    }

    private function createMockSpot(string $status = 'available'): ParkingSpot
    {
        $spot = $this->createMock(ParkingSpot::class);
        $spot->status = $status;

        return $spot;
    }

    public function test_should_throw_exception_when_creating_spot_with_duplicate_number(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('numberExists')
            ->with('A100')
            ->willReturn(true);

        $dto = new CreateParkingSpotDTO(
            number: 'A100',
            type: 'regular',
            hourly_price: 10.0,
            width: 2.5,
            length: 5.0
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Parking spot number already exists');

        $this->service->create($dto);
    }

    public function test_should_create_spot_with_unique_number(): void
    {
        $createdSpot = $this->createMock(ParkingSpot::class);

        $this->repository
            ->expects($this->once())
            ->method('numberExists')
            ->with('A100')
            ->willReturn(false);

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($data) {
                return $data['number'] === 'A100'
                    && $data['type'] === 'regular'
                    && $data['hourly_price'] === 10.0;
            }))
            ->willReturn($createdSpot);

        $dto = new CreateParkingSpotDTO(
            number: 'A100',
            type: 'regular',
            hourly_price: 10.0,
            width: 2.5,
            length: 5.0
        );

        $result = $this->service->create($dto);

        $this->assertInstanceOf(ParkingSpot::class, $result);
    }

    public function test_should_throw_exception_when_updating_to_duplicate_number(): void
    {
        $existingSpot = $this->createMock(ParkingSpot::class);
        $existingSpot->number = 'A101';

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($existingSpot);

        $this->repository
            ->expects($this->once())
            ->method('numberExists')
            ->with('A100')
            ->willReturn(true);

        $dto = new UpdateParkingSpotDTO(
            number: 'A100',
            type: 'regular',
            hourly_price: 10.0,
            width: 2.5,
            length: 5.0,
            status: 'available'
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Parking spot number already exists');

        $this->service->update(1, $dto);
    }

    public function test_should_throw_exception_when_updating_nonexistent_spot(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with(999999)
            ->willReturn(null);

        $dto = new UpdateParkingSpotDTO(
            number: 'A100',
            type: 'regular',
            hourly_price: 10.0,
            width: 2.5,
            length: 5.0,
            status: 'available'
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Parking spot not found');

        $this->service->update(999999, $dto);
    }

    public function test_should_throw_exception_when_deleting_occupied_spot(): void
    {
        $spot = $this->createMock(ParkingSpot::class);
        $spot->method('__get')->willReturnCallback(function ($name) {
            if ($name === 'status') {
                return 'occupied';
            }
            return null;
        });

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($spot);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete occupied parking spot');

        $this->service->delete(1);
    }

    public function test_should_delete_available_spot_successfully(): void
    {
        $spot = $this->createMockSpot('available');

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($spot);

        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willReturn(true);

        $result = $this->service->delete(1);

        $this->assertTrue($result);
    }

    public function test_should_throw_exception_when_deleting_nonexistent_spot(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with(999999)
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Parking spot not found');

        $this->service->delete(999999);
    }

    public function test_should_mark_spot_as_occupied(): void
    {
        $updatedSpot = $this->createMock(ParkingSpot::class);

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with(1, ['status' => 'occupied'])
            ->willReturn($updatedSpot);

        $result = $this->service->markAsOccupied(1);

        $this->assertInstanceOf(ParkingSpot::class, $result);
    }

    public function test_should_throw_exception_when_marking_as_occupied_fails(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with(1, ['status' => 'occupied'])
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Failed to mark parking spot as occupied');

        $this->service->markAsOccupied(1);
    }

    public function test_should_mark_spot_as_available(): void
    {
        $updatedSpot = $this->createMock(ParkingSpot::class);

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with(1, ['status' => 'available'])
            ->willReturn($updatedSpot);

        $result = $this->service->markAsAvailable(1);

        $this->assertInstanceOf(ParkingSpot::class, $result);
    }

    public function test_should_throw_exception_when_marking_as_available_fails(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with(1, ['status' => 'available'])
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Failed to mark parking spot as available');

        $this->service->markAsAvailable(1);
    }

    public function test_should_return_available_spots_collection(): void
    {
        $mockCollection = new Collection();

        $this->repository
            ->expects($this->once())
            ->method('getAvailable')
            ->willReturn($mockCollection);

        $result = $this->service->getAvailable();

        $this->assertInstanceOf(Collection::class, $result);
    }



    public function test_should_find_spot_by_id(): void
    {
        $spot = $this->createMock(ParkingSpot::class);

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($spot);

        $result = $this->service->findById(1);

        $this->assertInstanceOf(ParkingSpot::class, $result);
    }

    public function test_should_return_null_when_spot_not_found_by_id(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with(999)
            ->willReturn(null);

        $result = $this->service->findById(999);

        $this->assertNull($result);
    }
}
