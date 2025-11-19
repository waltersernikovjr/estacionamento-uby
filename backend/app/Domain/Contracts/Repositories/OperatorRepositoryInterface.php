<?php

namespace App\Domain\Contracts\Repositories;

use App\Infrastructure\Persistence\Models\Operator;
use Illuminate\Database\Eloquent\Collection;

interface OperatorRepositoryInterface
{
    /**
     * Find an operator by ID.
     */
    public function findById(int $id): ?Operator;

    /**
     * Find an operator by email.
     */
    public function findByEmail(string $email): ?Operator;

    /**
     * Find an operator by CPF.
     */
    public function findByCpf(string $cpf): ?Operator;

    /**
     * Get all operators.
     */
    public function all(): Collection;

    /**
     * Create a new operator.
     */
    public function create(array $data): Operator;

    /**
     * Update an operator.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete an operator.
     */
    public function delete(int $id): bool;

    /**
     * Check if email exists.
     */
    public function emailExists(string $email, ?int $excludeId = null): bool;

    /**
     * Check if CPF exists.
     */
    public function cpfExists(string $cpf, ?int $excludeId = null): bool;
}
