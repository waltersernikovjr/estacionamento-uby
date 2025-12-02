<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\DTOs\Payment\CreatePaymentDTO;
use App\Application\DTOs\Payment\UpdatePaymentDTO;
use App\Application\Services\PaymentService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService
    ) {
    }

    public function show(string $id): JsonResponse
    {
        $payment = $this->paymentService->findById((int) $id);

        if (!$payment) {
            return response()->json([
                'message' => 'Payment not found',
            ], 404);
        }

        return (new PaymentResource($payment))->response();
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        try {
            $dto = new CreatePaymentDTO(
                reservation_id: (int) $request->input('reservation_id'),
                amount: (float) $request->input('amount'),
                payment_method: $request->input('payment_method'),
                status: $request->input('status', 'pending'),
            );

            $payment = $this->paymentService->create($dto);

            return (new PaymentResource($payment))
                ->response()
                ->setStatusCode(201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update(UpdatePaymentRequest $request, string $id): JsonResponse
    {
        try {
            $dto = new UpdatePaymentDTO(
                payment_method: $request->input('payment_method'),
                status: $request->input('status'),
            );

            $payment = $this->paymentService->update((int) $id, $dto);

            return (new PaymentResource($payment))->response();
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function markAsPaid(string $id): JsonResponse
    {
        try {
            $payment = $this->paymentService->markAsPaid((int) $id);

            return (new PaymentResource($payment))->response();
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->paymentService->delete((int) $id);

            return response()->json(null, 204);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
