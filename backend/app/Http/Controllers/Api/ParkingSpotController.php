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
        $parkingSpots = $this->parkingSpotService->list();
        
        return ParkingSpotResource::collection($parkingSpots);
    }

    public function available(): JsonResponse
    {
        $parkingSpots = $this->parkingSpotService->getAvailable();
        
        return response()->json([
            'data' => ParkingSpotResource::collection($parkingSpots),
        ]);
    }

    public function store(StoreParkingSpotRequest $request): JsonResponse
    {
        try {
            $dto = new CreateParkingSpotDTO(
                number: $request->input('number'),
                type: $request->input('type'),
                status: $request->input('status', 'available'),
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
