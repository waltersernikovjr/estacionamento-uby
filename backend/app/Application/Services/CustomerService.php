<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\DTOs\Customer\CreateCustomerDTO;
use App\Application\DTOs\Customer\UpdateCustomerDTO;
use App\Domain\Contracts\Repositories\CustomerRepositoryInterface;
use App\Infrastructure\Persistence\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

final class CustomerService
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository
    ) {
    }

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->customerRepository->paginate($perPage);
    }

    public function findById(int $id): ?Customer
    {
        return $this->customerRepository->findById($id);
    }

    public function findWithVehicles(int $id): ?Customer
    {
        return $this->customerRepository->findWithVehicles($id);
    }

    public function findWithReservations(int $id): ?Customer
    {
        return $this->customerRepository->findWithReservations($id);
    }

    public function create(CreateCustomerDTO $dto): Customer
    {
        if ($this->customerRepository->emailExists($dto->email)) {
            throw new \InvalidArgumentException('Email already in use');
        }

        if ($this->customerRepository->cpfExists($dto->cpf)) {
            throw new \InvalidArgumentException('CPF already registered');
        }

        $data = $dto->toArray();
        $data['password'] = Hash::make($data['password']);

        return $this->customerRepository->create($data);
    }

    public function update(int $id, UpdateCustomerDTO $dto): Customer
    {
        $customer = $this->findById($id);
        
        if (!$customer) {
            throw new \InvalidArgumentException('Customer not found');
        }

        if ($dto->email && $dto->email !== $customer->email) {
            if ($this->customerRepository->emailExists($dto->email)) {
                throw new \InvalidArgumentException('Email already in use');
            }
        }

        if ($dto->cpf && $dto->cpf !== $customer->cpf) {
            if ($this->customerRepository->cpfExists($dto->cpf)) {
                throw new \InvalidArgumentException('CPF already registered');
            }
        }

        $data = $dto->toArray();
        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->customerRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $customer = $this->findById($id);
        
        if (!$customer) {
            throw new \InvalidArgumentException('Customer not found');
        }

        return $this->customerRepository->delete($id);
    }
}
