<?php

namespace App\Domain\Contracts\Repositories;

use App\Infrastructure\Persistence\Models\Customer;
use Illuminate\Database\Eloquent\Collection;

interface CustomerRepositoryInterface
{
    /**
     * Find a customer by ID.
     */
    public function findById(int $id): ?Customer;

    /**
     * Find a customer by email.
     */
    public function findByEmail(string $email): ?Customer;

    /**
     * Find a customer by CPF.
     */
    public function findByCpf(string $cpf): ?Customer;

    /**
     * Get all customers.
     */
    public function all(): Collection;

    /**
     * Create a new customer.
     */
    public function create(array $data): Customer;

    /**
     * Update a customer.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a customer.
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

    /**
     * Get customer with vehicles.
     */
    public function findWithVehicles(int $id): ?Customer;

    /**
     * Get customer with reservations.
     */
    public function findWithReservations(int $id): ?Customer;
}
