<?php

namespace Tests\Unit\Services;

use App\Application\DTOs\Vehicle\CreateVehicleDTO;
use App\Application\DTOs\Vehicle\UpdateVehicleDTO;
use App\Application\Services\VehicleService;
use App\Domain\Contracts\Repositories\VehicleRepositoryInterface;
use App\Infrastructure\Persistence\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class VehicleServiceTest extends TestCase
{
    private VehicleRepositoryInterface $repository;
    private VehicleService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(VehicleRepositoryInterface::class);
        $this->service = new VehicleService($this->repository);
    }

    public function test_creates_vehicle_with_valid_data(): void
    {
        $dto = new CreateVehicleDTO(
            customer_id: 1,
            license_plate: 'ABC1234',
            brand: 'Toyota',
            model: 'Corolla',
            color: 'Black',
            type: 'sedan'
        );

        $this->repository
            ->expects($this->once())
            ->method('licensePlateExists')
            ->with('ABC1234')
            ->willReturn(false);

        $vehicle = new Vehicle();
        $vehicle->id = 1;
        $vehicle->license_plate = 'ABC1234';

        $this->repository
            ->expects($this->once())
            ->method('create')
            ->with($dto->toArray())
            ->willReturn($vehicle);

        $result = $this->service->create($dto);

        $this->assertInstanceOf(Vehicle::class, $result);
        $this->assertEquals('ABC1234', $result->license_plate);
    }

    public function test_throws_exception_when_license_plate_already_exists(): void
    {
        $dto = new CreateVehicleDTO(
            customer_id: 1,
            license_plate: 'ABC1234',
            brand: 'Toyota',
            model: 'Corolla',
            color: 'Black',
            type: 'sedan'
        );

        $this->repository
            ->expects($this->once())
            ->method('licensePlateExists')
            ->with('ABC1234')
            ->willReturn(true);

        $this->repository
            ->expects($this->never())
            ->method('create');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('License plate already registered');

        $this->service->create($dto);
    }

    public function test_finds_vehicles_by_customer_id(): void
    {
        $customerId = 1;
        $vehicles = new Collection([new Vehicle(), new Vehicle()]);

        $this->repository
            ->expects($this->once())
            ->method('findByCustomer')
            ->with($customerId)
            ->willReturn($vehicles);

        $result = $this->service->findByCustomer($customerId);

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_updates_vehicle_successfully(): void
    {
        $vehicleId = 1;
        $dto = new UpdateVehicleDTO(
            license_plate: 'XYZ5678',
            brand: 'Honda',
            model: 'Civic',
            color: 'White',
            type: 'sedan'
        );

        $existingVehicle = new Vehicle();
        $existingVehicle->id = $vehicleId;
        $existingVehicle->license_plate = 'ABC1234';

        $updatedVehicle = new Vehicle();
        $updatedVehicle->id = $vehicleId;
        $updatedVehicle->license_plate = 'XYZ5678';

        $this->repository
            ->expects($this->exactly(2))
            ->method('findById')
            ->with($vehicleId)
            ->willReturnOnConsecutiveCalls($existingVehicle, $updatedVehicle);

        $this->repository
            ->expects($this->once())
            ->method('licensePlateExists')
            ->with('XYZ5678')
            ->willReturn(false);

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with($vehicleId, $dto->toArray());

        $result = $this->service->update($vehicleId, $dto);

        $this->assertEquals('XYZ5678', $result->license_plate);
    }

    public function test_throws_exception_when_updating_non_existent_vehicle(): void
    {
        $vehicleId = 999;
        $dto = new UpdateVehicleDTO(
            license_plate: 'XYZ5678',
            brand: 'Honda',
            model: 'Civic',
            color: 'White',
            type: 'sedan'
        );

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($vehicleId)
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Vehicle not found');

        $this->service->update($vehicleId, $dto);
    }

    public function test_throws_exception_when_updating_to_existing_license_plate(): void
    {
        $vehicleId = 1;
        $dto = new UpdateVehicleDTO(
            license_plate: 'XYZ5678',
            brand: 'Honda',
            model: 'Civic',
            color: 'White',
            type: 'sedan'
        );

        $existingVehicle = new Vehicle();
        $existingVehicle->id = $vehicleId;
        $existingVehicle->license_plate = 'ABC1234';

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($vehicleId)
            ->willReturn($existingVehicle);

        $this->repository
            ->expects($this->once())
            ->method('licensePlateExists')
            ->with('XYZ5678')
            ->willReturn(true);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('License plate already registered');

        $this->service->update($vehicleId, $dto);
    }

    public function test_deletes_vehicle_successfully(): void
    {
        $vehicleId = 1;
        $vehicle = new Vehicle();
        $vehicle->id = $vehicleId;

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($vehicleId)
            ->willReturn($vehicle);

        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with($vehicleId)
            ->willReturn(true);

        $result = $this->service->delete($vehicleId);

        $this->assertTrue($result);
    }

    public function test_throws_exception_when_deleting_non_existent_vehicle(): void
    {
        $vehicleId = 999;

        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($vehicleId)
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Vehicle not found');

        $this->service->delete($vehicleId);
    }
}
