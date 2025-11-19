<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Application\DTOs\CreatePaymentDTO;
use App\Application\DTOs\UpdatePaymentDTO;
use App\Application\Services\PaymentService;
use App\Domain\Entities\Payment;
use App\Domain\Repositories\PaymentRepositoryInterface;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Mockery;

class PaymentServiceTest extends TestCase
{
    private PaymentService $service;
    private PaymentRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(PaymentRepositoryInterface::class);
        $this->service = new PaymentService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_payment_successfully(): void
    {
        // Arrange
        $dto = new CreatePaymentDTO(
            reservationId: 1,
            amount: 25.00,
            paymentMethod: 'credit_card',
            status: 'pending'
        );

        $payment = new Payment();
        $payment->id = 1;
        $payment->reservation_id = 1;
        $payment->amount = 25.00;
        $payment->payment_method = 'credit_card';
        $payment->status = 'pending';

        $this->repository->shouldReceive('findByReservation')
            ->with(1)
            ->andReturn(null);

        $this->repository->shouldReceive('create')
            ->with($dto)
            ->andReturn($payment);

        // Act
        $result = $this->service->create($dto);

        // Assert
        $this->assertInstanceOf(Payment::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('pending', $result->status);
        $this->assertEquals(25.00, $result->amount);
    }

    public function test_create_payment_fails_when_reservation_already_has_payment(): void
    {
        // Arrange
        $dto = new CreatePaymentDTO(
            reservationId: 1,
            amount: 25.00,
            paymentMethod: 'credit_card',
            status: 'pending'
        );

        $existingPayment = new Payment();
        $existingPayment->id = 1;
        $existingPayment->reservation_id = 1;

        $this->repository->shouldReceive('findByReservation')
            ->with(1)
            ->andReturn($existingPayment);

        // Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Esta reserva já possui um pagamento');

        // Act
        $this->service->create($dto);
    }

    public function test_mark_as_paid_successfully(): void
    {
        // Arrange
        $payment = new Payment();
        $payment->id = 1;
        $payment->status = 'pending';

        $this->repository->shouldReceive('find')
            ->with(1)
            ->andReturn($payment);

        $this->repository->shouldReceive('update')
            ->once()
            ->andReturn($payment);

        // Act
        $result = $this->service->markAsPaid(1);

        // Assert
        $this->assertInstanceOf(Payment::class, $result);
        $this->assertEquals('paid', $result->status);
        $this->assertNotNull($result->paid_at);
    }

    public function test_mark_as_paid_fails_when_already_paid(): void
    {
        // Arrange
        $payment = new Payment();
        $payment->id = 1;
        $payment->status = 'paid';

        $this->repository->shouldReceive('find')
            ->with(1)
            ->andReturn($payment);

        // Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Este pagamento já foi processado');

        // Act
        $this->service->markAsPaid(1);
    }

    public function test_find_by_status_returns_collection(): void
    {
        // Arrange
        $payments = new Collection([
            new Payment(),
            new Payment()
        ]);

        $this->repository->shouldReceive('findByStatus')
            ->with('pending')
            ->andReturn($payments);

        // Act
        $result = $this->service->findByStatus('pending');

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }

    public function test_update_payment_successfully(): void
    {
        // Arrange
        $dto = new UpdatePaymentDTO(
            amount: 30.00,
            paymentMethod: 'pix',
            status: 'paid'
        );

        $payment = new Payment();
        $payment->id = 1;
        $payment->amount = 25.00;
        $payment->status = 'pending';

        $this->repository->shouldReceive('update')
            ->with(1, $dto)
            ->andReturn($payment);

        // Act
        $result = $this->service->update(1, $dto);

        // Assert
        $this->assertInstanceOf(Payment::class, $result);
    }

    public function test_delete_payment_successfully(): void
    {
        // Arrange
        $this->repository->shouldReceive('delete')
            ->with(1)
            ->andReturn(true);

        // Act
        $result = $this->service->delete(1);

        // Assert
        $this->assertTrue($result);
    }

    public function test_find_all_returns_collection(): void
    {
        // Arrange
        $payments = new Collection([
            new Payment(),
            new Payment(),
            new Payment()
        ]);

        $this->repository->shouldReceive('findAll')
            ->andReturn($payments);

        // Act
        $result = $this->service->findAll();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    }

    public function test_find_by_id_returns_payment(): void
    {
        // Arrange
        $payment = new Payment();
        $payment->id = 1;

        $this->repository->shouldReceive('find')
            ->with(1)
            ->andReturn($payment);

        // Act
        $result = $this->service->find(1);

        // Assert
        $this->assertInstanceOf(Payment::class, $result);
        $this->assertEquals(1, $result->id);
    }
}
