<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\DTOs\Payment\CreatePaymentDTO;
use App\Application\DTOs\Payment\UpdatePaymentDTO;
use App\Domain\Contracts\Repositories\PaymentRepositoryInterface;
use App\Infrastructure\Persistence\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

final class PaymentService
{
    public function __construct(
        private readonly PaymentRepositoryInterface $paymentRepository
    ) {
    }

    public function findById(int $id): ?Payment
    {
        return $this->paymentRepository->findById($id);
    }

    public function findByReservation(int $reservationId): ?Payment
    {
        return $this->paymentRepository->findByReservation($reservationId);
    }

    public function findPending(): Collection
    {
        return $this->paymentRepository->findByStatus('pending');
    }

    public function create(CreatePaymentDTO $dto): Payment
    {
        // Check if reservation already has a payment
        if ($this->findByReservation($dto->reservation_id)) {
            throw new \InvalidArgumentException('Reservation already has a payment');
        }

        return $this->paymentRepository->create($dto->toArray());
    }

    public function update(int $id, UpdatePaymentDTO $dto): Payment
    {
        $payment = $this->findById($id);
        
        if (!$payment) {
            throw new \InvalidArgumentException('Payment not found');
        }

        $this->paymentRepository->update($id, $dto->toArray());
        
        return $this->findById($id);
    }

    public function markAsPaid(int $id): Payment
    {
        $payment = $this->findById($id);
        
        if (!$payment) {
            throw new \InvalidArgumentException('Payment not found');
        }

        if ($payment->status === 'paid') {
            throw new \InvalidArgumentException('Payment already marked as paid');
        }

        $this->paymentRepository->update($id, [
            'status' => 'paid',
            'paid_at' => Carbon::now(),
        ]);

        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $payment = $this->findById($id);
        
        if (!$payment) {
            throw new \InvalidArgumentException('Payment not found');
        }

        return $this->paymentRepository->delete($id);
    }
}
