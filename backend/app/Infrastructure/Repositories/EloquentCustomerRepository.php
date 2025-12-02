<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\Repositories\CustomerRepositoryInterface;
use App\Infrastructure\Persistence\Models\Customer;
use Illuminate\Database\Eloquent\Collection;

final class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function findById(int $id): ?Customer
    {
        return Customer::find($id);
    }

    public function findByEmail(string $email): ?Customer
    {
        return Customer::where('email', $email)->first();
    }

    public function findByCpf(string $cpf): ?Customer
    {
        return Customer::where('cpf', $cpf)->first();
    }

    public function all(): Collection
    {
        return Customer::all();
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Customer::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Customer::where('id', $id)->delete();
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $query = Customer::where('email', $email);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    public function cpfExists(string $cpf, ?int $excludeId = null): bool
    {
        $query = Customer::where('cpf', $cpf);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    public function findWithVehicles(int $id): ?Customer
    {
        return Customer::with('vehicles')->find($id);
    }

    public function findWithReservations(int $id): ?Customer
    {
        return Customer::with(['reservations.parkingSpot', 'reservations.vehicle'])->find($id);
    }
}
