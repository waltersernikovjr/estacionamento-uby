<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories;

use App\Infrastructure\Persistence\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PaymentRepositoryInterface
{
    public function findById(int $id): ?Payment;
    
    public function create(array $data): Payment;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    
    public function findByReservation(int $reservationId): ?Payment;
    
    public function getPending(): Collection;
    
    public function getPaid(): Collection;
}
