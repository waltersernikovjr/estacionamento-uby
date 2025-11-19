<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Entities\Customer;
use App\Domain\Entities\Operator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register(): void
    {
        $response = $this->postJson('/api/v1/customers/register', [
            'name' => 'João Silva',
            'email' => 'joao@test.com',
            'cpf' => '12345678900',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '11999999999',
            'street' => 'Rua Teste',
            'neighborhood' => 'Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01310100'
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'user' => [
                'id',
                'name',
                'email',
                'cpf',
                'type'
            ],
            'token'
        ]);

        $this->assertDatabaseHas('customers', [
            'email' => 'joao@test.com',
            'cpf' => '12345678900'
        ]);
    }

    public function test_customer_cannot_register_with_duplicate_email(): void
    {
        Customer::factory()->create([
            'email' => 'existing@test.com'
        ]);

        $response = $this->postJson('/api/v1/customers/register', [
            'name' => 'João Silva',
            'email' => 'existing@test.com',
            'cpf' => '12345678900',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_customer_can_login(): void
    {
        $customer = Customer::factory()->create([
            'email' => 'customer@test.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/v1/customers/login', [
            'email' => 'customer@test.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user' => ['id', 'name', 'email', 'type'],
            'token'
        ]);
    }

    public function test_customer_cannot_login_with_wrong_password(): void
    {
        Customer::factory()->create([
            'email' => 'customer@test.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/v1/customers/login', [
            'email' => 'customer@test.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401);
    }

    public function test_customer_can_logout(): void
    {
        $customer = Customer::factory()->create();
        $token = $customer->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/customers/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Logout realizado com sucesso'
        ]);
    }

    public function test_customer_can_get_profile_info(): void
    {
        $customer = Customer::factory()->create([
            'name' => 'João Silva',
            'email' => 'joao@test.com'
        ]);
        $token = $customer->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/customers/me');

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'name' => 'João Silva',
                'email' => 'joao@test.com',
                'type' => 'customer'
            ]
        ]);
    }

    public function test_operator_can_register(): void
    {
        $response = $this->postJson('/api/v1/operators/register', [
            'name' => 'Operador Teste',
            'email' => 'operator@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '11988888888'
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'user' => [
                'id',
                'name',
                'email',
                'type'
            ],
            'token'
        ]);

        $this->assertDatabaseHas('operators', [
            'email' => 'operator@test.com'
        ]);
    }

    public function test_operator_can_login(): void
    {
        $operator = Operator::factory()->create([
            'email' => 'operator@test.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/v1/operators/login', [
            'email' => 'operator@test.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user' => ['id', 'name', 'email', 'type'],
            'token'
        ]);
    }

    public function test_operator_can_logout(): void
    {
        $operator = Operator::factory()->create();
        $token = $operator->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/operators/logout');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_protected_routes(): void
    {
        $response = $this->getJson('/api/v1/customers/me');

        $response->assertStatus(401);
    }

    public function test_customer_cannot_register_with_invalid_cpf(): void
    {
        $response = $this->postJson('/api/v1/customers/register', [
            'name' => 'João Silva',
            'email' => 'joao@test.com',
            'cpf' => '123', // CPF inválido
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['cpf']);
    }

    public function test_password_confirmation_must_match(): void
    {
        $response = $this->postJson('/api/v1/customers/register', [
            'name' => 'João Silva',
            'email' => 'joao@test.com',
            'cpf' => '12345678900',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }
}
