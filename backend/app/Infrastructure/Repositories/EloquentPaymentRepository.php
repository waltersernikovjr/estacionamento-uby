<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\Repositories\PaymentRepositoryInterface;
use App\Infrastructure\Persistence\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class EloquentPaymentRepository implements PaymentRepositoryInterface
{
    public function findById(int $id): ?Payment
    {
        return Payment::find($id);
    }

    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Payment::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Payment::destroy($id) > 0;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Payment::with('reservation')->paginate($perPage);
    }

    public function findByReservation(int $reservationId): ?Payment
    {
        return Payment::where('reservation_id', $reservationId)->first();
    }

    public function getPending(): Collection
    {
        return Payment::pending()->get();
    }

    public function getPaid(): Collection
    {
        return Payment::paid()->get();
    }
}
