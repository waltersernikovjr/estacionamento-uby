<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Entities\Customer;
use App\Domain\Entities\Operator;
use App\Domain\Entities\ParkingSpot;
use App\Domain\Entities\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationFlowTest extends TestCase
{
    use RefreshDatabase;

    private string $customerToken;
    private Customer $customer;
    private Vehicle $vehicle;
    private ParkingSpot $parkingSpot;

    protected function setUp(): void
    {
        parent::setUp();

        // Create customer and authenticate
        $this->customer = Customer::factory()->create([
            'email' => 'customer@test.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/v1/customers/login', [
            'email' => 'customer@test.com',
            'password' => 'password123'
        ]);

        $this->customerToken = $response->json('token');

        // Create vehicle
        $this->vehicle = Vehicle::factory()->create([
            'customer_id' => $this->customer->id,
            'license_plate' => 'ABC1234'
        ]);

        // Create available parking spot
        $this->parkingSpot = ParkingSpot::factory()->create([
            'number' => 'A01',
            'status' => 'available'
        ]);
    }

    public function test_complete_reservation_and_payment_flow(): void
    {
        $reservationResponse = $this->withToken($this->customerToken)
            ->postJson('/api/v1/reservations', [
                'customer_id' => $this->customer->id,
                'vehicle_id' => $this->vehicle->id,
                'parking_spot_id' => $this->parkingSpot->id,
                'entry_time' => now()->toISOString(),
                'expected_exit_time' => now()->addHours(2)->toISOString()
            ]);

        $reservationResponse->assertStatus(201);
        $reservationResponse->assertJsonStructure([
            'data' => [
                'id',
                'customer_id',
                'vehicle_id',
                'parking_spot_id',
                'entry_time',
                'status'
            ]
        ]);

        $reservationId = $reservationResponse->json('data.id');

        $completeResponse = $this->withToken($this->customerToken)
            ->postJson("/api/v1/reservations/{$reservationId}/complete", [
                'exit_time' => now()->addHours(2)->toISOString()
            ]);

        $completeResponse->assertStatus(200);
        $completeResponse->assertJson([
            'data' => [
                'status' => 'completed'
            ]
        ]);

        $totalAmount = $completeResponse->json('data.total_amount');
        $this->assertGreaterThan(0, $totalAmount);

        $paymentResponse = $this->withToken($this->customerToken)
            ->postJson('/api/v1/payments', [
                'reservation_id' => $reservationId,
                'amount' => $totalAmount,
                'payment_method' => 'credit_card',
                'status' => 'pending'
            ]);

        $paymentResponse->assertStatus(201);
        $paymentResponse->assertJsonStructure([
            'data' => [
                'id',
                'reservation_id',
                'amount',
                'payment_method',
                'status'
            ]
        ]);

        $paymentId = $paymentResponse->json('data.id');

        $paidResponse = $this->withToken($this->customerToken)
            ->postJson("/api/v1/payments/{$paymentId}/mark-as-paid");

        $paidResponse->assertStatus(200);
        $paidResponse->assertJson([
            'data' => [
                'status' => 'paid'
            ]
        ]);
        $paidResponse->assertJsonPath('data.paid_at', fn($date) => !is_null($date));
    }

    public function test_cannot_create_reservation_for_occupied_spot(): void
    {
        // Create occupied parking spot
        $occupiedSpot = ParkingSpot::factory()->create([
            'number' => 'B01',
            'status' => 'occupied'
        ]);

        $response = $this->withToken($this->customerToken)
            ->postJson('/api/v1/reservations', [
                'customer_id' => $this->customer->id,
                'vehicle_id' => $this->vehicle->id,
                'parking_spot_id' => $occupiedSpot->id,
                'entry_time' => now()->toISOString()
            ]);

        $response->assertStatus(422);
    }

    public function test_cannot_create_duplicate_payment_for_reservation(): void
    {
        // Create reservation
        $reservation = $this->withToken($this->customerToken)
            ->postJson('/api/v1/reservations', [
                'customer_id' => $this->customer->id,
                'vehicle_id' => $this->vehicle->id,
                'parking_spot_id' => $this->parkingSpot->id,
                'entry_time' => now()->toISOString()
            ])
            ->json('data');

        // Create first payment
        $this->withToken($this->customerToken)
            ->postJson('/api/v1/payments', [
                'reservation_id' => $reservation['id'],
                'amount' => 25.00,
                'payment_method' => 'credit_card',
                'status' => 'pending'
            ])
            ->assertStatus(201);

        // Try to create second payment (should fail)
        $response = $this->withToken($this->customerToken)
            ->postJson('/api/v1/payments', [
                'reservation_id' => $reservation['id'],
                'amount' => 25.00,
                'payment_method' => 'pix',
                'status' => 'pending'
            ]);

        $response->assertStatus(422);
    }

    public function test_list_available_parking_spots(): void
    {
        // Create more spots
        ParkingSpot::factory()->create(['status' => 'available']);
        ParkingSpot::factory()->create(['status' => 'occupied']);
        ParkingSpot::factory()->create(['status' => 'available']);

        $response = $this->withToken($this->customerToken)
            ->getJson('/api/v1/parking-spots-available');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'number', 'status', 'type']
            ]
        ]);

        $availableSpots = $response->json('data');
        $this->assertGreaterThanOrEqual(3, count($availableSpots));
        
        foreach ($availableSpots as $spot) {
            $this->assertEquals('available', $spot['status']);
        }
    }

    public function test_customer_can_view_their_reservations(): void
    {
        // Create multiple reservations
        $this->withToken($this->customerToken)
            ->postJson('/api/v1/reservations', [
                'customer_id' => $this->customer->id,
                'vehicle_id' => $this->vehicle->id,
                'parking_spot_id' => $this->parkingSpot->id,
                'entry_time' => now()->toISOString()
            ]);

        $response = $this->withToken($this->customerToken)
            ->getJson('/api/v1/reservations');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'customer_id',
                    'vehicle_id',
                    'parking_spot_id',
                    'status'
                ]
            ]
        ]);

        $reservations = $response->json('data');
        $this->assertGreaterThan(0, count($reservations));
    }

    private function withToken(string $token): self
    {
        return $this->withHeader('Authorization', "Bearer {$token}");
    }
}
