<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\DTOs\Operator\CreateOperatorDTO;
use App\Application\DTOs\Operator\UpdateOperatorDTO;
use App\Domain\Contracts\Repositories\OperatorRepositoryInterface;
use App\Infrastructure\Persistence\Models\Operator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

final class OperatorService
{
    public function __construct(
        private readonly OperatorRepositoryInterface $operatorRepository
    ) {
    }

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->operatorRepository->paginate($perPage);
    }

    public function findById(int $id): ?Operator
    {
        return $this->operatorRepository->findById($id);
    }

    public function create(CreateOperatorDTO $dto): Operator
    {
        if ($this->operatorRepository->emailExists($dto->email)) {
            throw new \InvalidArgumentException('Email already in use');
        }

        $data = $dto->toArray();
        $data['password'] = Hash::make($data['password']);

        return $this->operatorRepository->create($data);
    }

    public function update(int $id, UpdateOperatorDTO $dto): Operator
    {
        $operator = $this->findById($id);
        
        if (!$operator) {
            throw new \InvalidArgumentException('Operator not found');
        }

        if ($dto->email && $dto->email !== $operator->email) {
            if ($this->operatorRepository->emailExists($dto->email)) {
                throw new \InvalidArgumentException('Email already in use');
            }
        }

        $data = $dto->toArray();
        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->operatorRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $operator = $this->findById($id);
        
        if (!$operator) {
            throw new \InvalidArgumentException('Operator not found');
        }

        return $this->operatorRepository->delete($id);
    }
}
