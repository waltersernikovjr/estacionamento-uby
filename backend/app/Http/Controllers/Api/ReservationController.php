<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\DTOs\Reservation\CreateReservationDTO;
use App\Application\Services\ReservationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompleteReservationRequest;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Resources\ReservationResource;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReservationController extends Controller
{
    public function __construct(
        private readonly ReservationService $reservationService
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        $reservations = $this->reservationService->list();
        
        return ReservationResource::collection($reservations);
    }

    public function store(StoreReservationRequest $request): JsonResponse
    {
        try {
            $dto = new CreateReservationDTO(
                customer_id: (int) $request->input('customer_id'),
                vehicle_id: (int) $request->input('vehicle_id'),
                parking_spot_id: (int) $request->input('parking_spot_id'),
                entry_time: Carbon::parse($request->input('entry_time')),
                expected_exit_time: $request->has('expected_exit_time') 
                    ? Carbon::parse($request->input('expected_exit_time'))
                    : null,
            );

            $reservation = $this->reservationService->create($dto);

            return (new ReservationResource($reservation))
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
        $reservation = $this->reservationService->findById((int) $id);

        if (!$reservation) {
            return response()->json([
                'message' => 'Reservation not found',
            ], 404);
        }

        return (new ReservationResource($reservation))->response();
    }

    public function complete(CompleteReservationRequest $request, string $id): JsonResponse
    {
        try {
            $exitTime = Carbon::parse($request->input('exit_time'));
            $reservation = $this->reservationService->complete((int) $id, $exitTime);

            return (new ReservationResource($reservation))->response();
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function cancel(string $id): JsonResponse
    {
        try {
            $reservation = $this->reservationService->cancel((int) $id);

            return (new ReservationResource($reservation))->response();
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        return response()->json([
            'message' => 'Reservations cannot be deleted. Use cancel instead.',
        ], 405);
    }
}
