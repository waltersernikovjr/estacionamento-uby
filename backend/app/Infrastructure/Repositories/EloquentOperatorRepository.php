<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\Repositories\OperatorRepositoryInterface;
use App\Infrastructure\Persistence\Models\Operator;
use Illuminate\Database\Eloquent\Collection;

final class EloquentOperatorRepository implements OperatorRepositoryInterface
{
    public function findById(int $id): ?Operator
    {
        return Operator::find($id);
    }

    public function findByEmail(string $email): ?Operator
    {
        return Operator::where('email', $email)->first();
    }

    public function findByCpf(string $cpf): ?Operator
    {
        return Operator::where('cpf', $cpf)->first();
    }

    public function all(): Collection
    {
        return Operator::all();
    }

    public function create(array $data): Operator
    {
        return Operator::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Operator::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Operator::where('id', $id)->delete();
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $query = Operator::where('email', $email);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    public function cpfExists(string $cpf, ?int $excludeId = null): bool
    {
        $query = Operator::where('cpf', $cpf);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
}
