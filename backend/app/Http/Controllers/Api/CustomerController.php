<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\DTOs\Customer\CreateCustomerDTO;
use App\Application\DTOs\Customer\UpdateCustomerDTO;
use App\Application\Services\CustomerService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        $customers = $this->customerService->list();
        
        return CustomerResource::collection($customers);
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        try {
            $dto = new CreateCustomerDTO(
                name: $request->input('name'),
                email: $request->input('email'),
                cpf: $request->input('cpf'),
                password: $request->input('password'),
                phone: $request->input('phone'),
                street: $request->input('street'),
                neighborhood: $request->input('neighborhood'),
                city: $request->input('city'),
                state: $request->input('state'),
                zip_code: $request->input('zip_code'),
            );

            $customer = $this->customerService->create($dto);

            return (new CustomerResource($customer))
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
        $customer = $this->customerService->findById((int) $id);

        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found',
            ], 404);
        }

        return (new CustomerResource($customer))->response();
    }

    public function update(UpdateCustomerRequest $request, string $id): JsonResponse
    {
        try {
            $dto = new UpdateCustomerDTO(
                name: $request->input('name'),
                email: $request->input('email'),
                cpf: $request->input('cpf'),
                password: $request->input('password'),
                phone: $request->input('phone'),
                street: $request->input('street'),
                neighborhood: $request->input('neighborhood'),
                city: $request->input('city'),
                state: $request->input('state'),
                zip_code: $request->input('zip_code'),
            );

            $customer = $this->customerService->update((int) $id, $dto);

            return (new CustomerResource($customer))->response();
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->customerService->delete((int) $id);

            return response()->json(null, 204);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
