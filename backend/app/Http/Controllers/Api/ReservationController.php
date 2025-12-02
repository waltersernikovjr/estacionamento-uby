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
        if (auth()->user() instanceof \App\Infrastructure\Persistence\Models\Operator) {
            $reservations = $this->reservationService->listAll();
            return ReservationResource::collection($reservations);
        }

        $customerId = auth()->user()->id;
        $reservations = $this->reservationService->findByCustomer((int) $customerId);

        return ReservationResource::collection($reservations);
    }

    public function searchByPlate(): AnonymousResourceCollection
    {
        $plate = request()->input('plate', '');

        if (empty($plate)) {
            return ReservationResource::collection([]);
        }

        $vehicle = \App\Infrastructure\Persistence\Models\Vehicle::where('license_plate', 'LIKE', "%{$plate}%")->first();

        if (!$vehicle) {
            return ReservationResource::collection([]);
        }

        $reservations = \App\Infrastructure\Persistence\Models\Reservation::where('vehicle_id', $vehicle->id)
            ->with(['customer', 'vehicle', 'parkingSpot'])
            ->orderBy('created_at', 'desc')
            ->get();

        return ReservationResource::collection($reservations);
    }

    public function getActiveBySpot(int $spotId): JsonResponse
    {
        $reservation = \App\Infrastructure\Persistence\Models\Reservation::where('parking_spot_id', $spotId)
            ->where('status', 'active')
            ->with(['customer', 'vehicle', 'parkingSpot'])
            ->first();

        if (!$reservation) {
            return response()->json([
                'message' => 'Nenhuma reserva ativa encontrada para esta vaga',
            ], 404);
        }

        return (new ReservationResource($reservation))->response();
    }

    public function operatorFinalize(int $id, CompleteReservationRequest $request): JsonResponse
    {
        try {
            $exitTime = Carbon::parse($request->input('exit_time'));
            $operatorNotes = $request->input('operator_notes');
            $operatorId = auth()->user()->id;

            $reservation = $this->reservationService->complete($id, $exitTime);

            $reservationModel = \App\Infrastructure\Persistence\Models\Reservation::find($id);
            if ($reservationModel) {
                $reservationModel->operator_notes = $operatorNotes;
                $reservationModel->finalized_by_operator_id = $operatorId;
                $reservationModel->save();
            }

            return (new ReservationResource($reservation))->response();
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store(StoreReservationRequest $request): JsonResponse
    {
        try {
            $customerId = $request->input('customer_id') ?? auth()->user()->id;
            $vehicleId = (int) $request->input('vehicle_id');
            $parkingSpotId = (int) $request->input('parking_spot_id');

            if ($this->reservationService->hasActiveReservationForVehicle($vehicleId)) {
                return response()->json([
                    'message' => 'Este veículo já possui uma reserva ativa',
                ], 422);
            }

            $vehicle = \App\Infrastructure\Persistence\Models\Vehicle::find($vehicleId);
            $parkingSpot = \App\Infrastructure\Persistence\Models\ParkingSpot::find($parkingSpotId);

            if (!$vehicle || !$parkingSpot) {
                return response()->json([
                    'message' => 'Veículo ou vaga não encontrados',
                ], 404);
            }

            $vehicleType = $vehicle->type;
            $spotType = $parkingSpot->type;

            $mappedSpotType = match($spotType) {
                'vip' => 'truck',
                'disabled' => 'car',
                default => 'car',
            };

            if ($vehicleType !== $mappedSpotType) {
                $typeNames = [
                    'car' => 'carros',
                    'motorcycle' => 'motos',
                    'truck' => 'caminhões',
                    'van' => 'vans',
                ];

                $spotTypeNames = [
                    'regular' => 'carros',
                    'vip' => 'caminhões',
                    'disabled' => 'carros (PCD)',
                ];

                return response()->json([
                    'message' => "Esta vaga ({$spotTypeNames[$spotType]}) não é compatível com seu veículo ({$typeNames[$vehicleType]}).",
                ], 422);
            }

            $dto = new CreateReservationDTO(
                customer_id: (int) $customerId,
                vehicle_id: $vehicleId,
                parking_spot_id: $parkingSpotId,
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
