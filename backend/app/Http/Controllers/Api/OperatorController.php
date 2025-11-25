<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\DTOs\Operator\CreateOperatorDTO;
use App\Application\DTOs\Operator\UpdateOperatorDTO;
use App\Application\Services\OperatorService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOperatorRequest;
use App\Http\Requests\UpdateOperatorRequest;
use App\Http\Resources\OperatorResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OperatorController extends Controller
{
    public function __construct(
        private readonly OperatorService $operatorService
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        $operators = $this->operatorService->list();

        return OperatorResource::collection($operators);
    }

    public function store(StoreOperatorRequest $request): JsonResponse
    {
        try {
            $dto = new CreateOperatorDTO(
                name: $request->input('name'),
                email: $request->input('email'),
                password: $request->input('password'),
                phone: $request->input('phone'),
            );

            $operator = $this->operatorService->create($dto);

            return (new OperatorResource($operator))
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
        $operator = $this->operatorService->findById((int) $id);

        if (!$operator) {
            return response()->json([
                'message' => 'Operator not found',
            ], 404);
        }

        return (new OperatorResource($operator))->response();
    }

    public function update(UpdateOperatorRequest $request, string $id): JsonResponse
    {
        try {
            $dto = new UpdateOperatorDTO(
                name: $request->input('name'),
                email: $request->input('email'),
                password: $request->input('password'),
                phone: $request->input('phone'),
            );

            $operator = $this->operatorService->update((int) $id, $dto);

            return (new OperatorResource($operator))->response();
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->operatorService->delete((int) $id);

            return response()->json(null, 204);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function stats(): JsonResponse
    {
        $totalSpots = \App\Infrastructure\Persistence\Models\ParkingSpot::count();
        $availableSpots = \App\Infrastructure\Persistence\Models\ParkingSpot::where('status', 'available')->count();
        $activeReservations = \App\Infrastructure\Persistence\Models\Reservation::where('status', 'active')->count();

        $todayRevenue = \App\Infrastructure\Persistence\Models\Reservation::where('status', 'completed')
            ->whereDate('exit_time', today())
            ->sum('total_amount');

        return response()->json([
            'data' => [
                'total_spots' => $totalSpots,
                'available_spots' => $availableSpots,
                'active_reservations' => $activeReservations,
                'today_revenue' => (float) $todayRevenue,
            ],
        ]);
    }
}
