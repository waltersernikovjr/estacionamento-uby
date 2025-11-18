<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Services\ViaCepService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct(
        private readonly ViaCepService $viaCepService
    ) {
    }

    public function getByZipCode(Request $request, string $zipCode): JsonResponse
    {
        try {
            $address = $this->viaCepService->getAddress($zipCode);

            if (!$address) {
                return response()->json([
                    'message' => 'CEP não encontrado',
                ], 404);
            }

            return response()->json([
                'data' => $address,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

