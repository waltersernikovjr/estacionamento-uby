<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\DTOs\Vehicle\CreateVehicleDTO;
use App\Application\DTOs\Vehicle\UpdateVehicleDTO;
use App\Application\Services\VehicleService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Http\Resources\VehicleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VehicleController extends Controller
{
    public function __construct(
        private readonly VehicleService $vehicleService
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        if ($request->has('customer_id')) {
            $vehicles = $this->vehicleService->findByCustomer((int) $request->input('customer_id'));
            return VehicleResource::collection($vehicles);
        }

        return VehicleResource::collection([]);
    }

    public function store(StoreVehicleRequest $request): JsonResponse
    {
        try {
            $dto = new CreateVehicleDTO(
                customer_id: (int) $request->input('customer_id'),
                license_plate: $request->input('license_plate'),
                brand: $request->input('brand'),
                model: $request->input('model'),
                color: $request->input('color'),
                type: $request->input('type'),
            );

            $vehicle = $this->vehicleService->create($dto);

            return (new VehicleResource($vehicle))
                ->response()
                ->setStatusCode(201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(string $id): JsonResponse
    {
        $vehicle = $this->vehicleService->findById((int) $id);

        if (!$vehicle) {
            return response()->json([
                'message' => 'Vehicle not found',
            ], 404);
        }

        return (new VehicleResource($vehicle))->response();
    }

    public function update(UpdateVehicleRequest $request, string $id): JsonResponse
    {
        try {
            $dto = new UpdateVehicleDTO(
                license_plate: $request->input('license_plate'),
                brand: $request->input('brand'),
                model: $request->input('model'),
                color: $request->input('color'),
                type: $request->input('type'),
            );

            $vehicle = $this->vehicleService->update((int) $id, $dto);

            return (new VehicleResource($vehicle))->response();
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->vehicleService->delete((int) $id);

            return response()->json(null, 204);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
