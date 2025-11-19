<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Application\DTOs\CreateVehicleDTO;
use App\Application\DTOs\UpdateVehicleDTO;
use App\Application\Services\VehicleService;
use App\Domain\Entities\Vehicle;
use App\Domain\Repositories\VehicleRepositoryInterface;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Mockery;

class VehicleServiceTest extends TestCase
{
    private VehicleService $service;
    private VehicleRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(VehicleRepositoryInterface::class);
        $this->service = new VehicleService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_vehicle_successfully(): void
    {
        $dto = new CreateVehicleDTO(
            customerId: 1,
            licensePlate: 'ABC1234',
            brand: 'Toyota',
            model: 'Corolla',
            color: 'Prata',
            type: 'car'
        );

        $vehicle = new Vehicle();
        $vehicle->id = 1;
        $vehicle->license_plate = 'ABC1234';

        $this->repository->shouldReceive('licensePlateExists')
            ->with('ABC1234')
            ->andReturn(false);

        $this->repository->shouldReceive('create')
            ->with($dto)
            ->andReturn($vehicle);

        $result = $this->service->create($dto);

        $this->assertInstanceOf(Vehicle::class, $result);
        $this->assertEquals('ABC1234', $result->license_plate);
    }

    public function test_create_vehicle_fails_when_license_plate_exists(): void
    {
        $dto = new CreateVehicleDTO(
            customerId: 1,
            licensePlate: 'ABC1234',
            brand: 'Toyota',
            model: 'Corolla',
            color: 'Prata',
            type: 'car'
        );

        $this->repository->shouldReceive('licensePlateExists')
            ->with('ABC1234')
            ->andReturn(true);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Placa já cadastrada');

        $this->service->create($dto);
    }

    public function test_update_vehicle_successfully(): void
    {
        $dto = new UpdateVehicleDTO(
            brand: 'Honda',
            model: 'Civic',
            color: 'Preto'
        );

        $vehicle = new Vehicle();
        $vehicle->id = 1;

        $this->repository->shouldReceive('update')
            ->with(1, $dto)
            ->andReturn($vehicle);

        $result = $this->service->update(1, $dto);

        $this->assertInstanceOf(Vehicle::class, $result);
    }

    public function test_find_by_customer_returns_collection(): void
    {
        $vehicles = new Collection([
            new Vehicle(),
            new Vehicle()
        ]);

        $this->repository->shouldReceive('findByCustomer')
            ->with(1)
            ->andReturn($vehicles);

        $result = $this->service->findByCustomer(1);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }

    public function test_find_by_license_plate_returns_vehicle(): void
    {
        $vehicle = new Vehicle();
        $vehicle->id = 1;
        $vehicle->license_plate = 'ABC1234';

        $this->repository->shouldReceive('findByLicensePlate')
            ->with('ABC1234')
            ->andReturn($vehicle);

        $result = $this->service->findByLicensePlate('ABC1234');

        $this->assertInstanceOf(Vehicle::class, $result);
        $this->assertEquals('ABC1234', $result->license_plate);
    }

    public function test_delete_vehicle_successfully(): void
    {
        $this->repository->shouldReceive('delete')
            ->with(1)
            ->andReturn(true);

        $result = $this->service->delete(1);

        $this->assertTrue($result);
    }

    public function test_find_all_returns_collection(): void
    {
        $vehicles = new Collection([
            new Vehicle(),
            new Vehicle(),
            new Vehicle()
        ]);

        $this->repository->shouldReceive('findAll')
            ->andReturn($vehicles);

        $result = $this->service->findAll();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    }

    public function test_find_by_id_returns_vehicle(): void
    {
        $vehicle = new Vehicle();
        $vehicle->id = 1;

        $this->repository->shouldReceive('find')
            ->with(1)
            ->andReturn($vehicle);

        $result = $this->service->find(1);

        $this->assertInstanceOf(Vehicle::class, $result);
        $this->assertEquals(1, $result->id);
    }
}
