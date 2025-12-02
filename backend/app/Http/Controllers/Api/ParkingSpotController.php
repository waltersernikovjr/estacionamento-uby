<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\DTOs\ParkingSpot\CreateParkingSpotDTO;
use App\Application\DTOs\ParkingSpot\UpdateParkingSpotDTO;
use App\Application\Services\ParkingSpotService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreParkingSpotRequest;
use App\Http\Requests\UpdateParkingSpotRequest;
use App\Http\Resources\ParkingSpotResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ParkingSpotController extends Controller
{
    public function __construct(
        private readonly ParkingSpotService $parkingSpotService
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        $parkingSpots = $this->parkingSpotService->listAll();

        return ParkingSpotResource::collection($parkingSpots);
    }

    public function available(): AnonymousResourceCollection
    {
        $parkingSpots = $this->parkingSpotService->getAvailable();

        return ParkingSpotResource::collection($parkingSpots);
    }

    public function store(StoreParkingSpotRequest $request): JsonResponse
    {
        try {
            $operatorId = $request->input('operator_id') ?? auth()->user()->id;

            $dto = new CreateParkingSpotDTO(
                number: $request->input('number'),
                type: $request->input('type'),
                hourly_price: (float) $request->input('hourly_price', 5.00),
                width: (float) $request->input('width', 2.50),
                length: (float) $request->input('length', 5.00),
                status: $request->input('status', 'available'),
                operator_id: $operatorId,
            );

            $parkingSpot = $this->parkingSpotService->create($dto);

            return (new ParkingSpotResource($parkingSpot))
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
        $parkingSpot = $this->parkingSpotService->findById((int) $id);

        if (!$parkingSpot) {
            return response()->json([
                'message' => 'Parking spot not found',
            ], 404);
        }

        return (new ParkingSpotResource($parkingSpot))->response();
    }

    public function update(UpdateParkingSpotRequest $request, string $id): JsonResponse
    {
        try {
            $dto = new UpdateParkingSpotDTO(
                number: $request->input('number'),
                type: $request->input('type'),
                status: $request->input('status'),
                hourly_price: $request->has('hourly_price') ? (float) $request->input('hourly_price') : null,
                width: $request->has('width') ? (float) $request->input('width') : null,
                length: $request->has('length') ? (float) $request->input('length') : null,
            );

            $parkingSpot = $this->parkingSpotService->update((int) $id, $dto);

            return (new ParkingSpotResource($parkingSpot))->response();
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->parkingSpotService->delete((int) $id);

            return response()->json(null, 204);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
