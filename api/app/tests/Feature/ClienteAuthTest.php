<?php

namespace Tests\Feature;

use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_cliente_pode_se_cadastrar_com_veiculo()
    {
        $payload = [
            'nome' => 'Maria Silva',
            'cpf' => '98765432100',
            'rg' => '987654321',
            'endereco' => 'Av. Brasil, 100',
            'password' => 'maria123',
            'password_confirmation' => 'maria123',
            'veiculo' => [
                'placa' => 'XYZ9876',
                'modelo' => 'Toyota Corolla',
                'cor' => 'Preto',
                'ano' => 2023,
            ]
        ];

        $response = $this->postJson('/api/cliente/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure(['access_token', 'cliente']);

        $this->assertDatabaseHas('clientes', ['cpf' => '98765432100']);
        $this->assertDatabaseHas('veiculos', ['placa' => 'XYZ9876']);
    }

    public function test_cliente_pode_fazer_login_com_cpf_e_senha()
    {
        $cliente = Cliente::factory()->create([
            'cpf' => '11122233344',
            'password' => bcrypt('cliente123'),
        ]);

        $response = $this->postJson('/api/cliente/login', [
            'cpf' => '11122233344',
            'password' => 'cliente123',
        ]);


        $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'cliente']);
    }

    public function test_nao_permite_cadastro_com_placa_duplicada()
    {
        $cliente1 = Cliente::factory()->create();
        $cliente1->veiculos()->create([
            'placa' => 'ABC1234',
            'modelo' => 'Fiat Uno',
            'cor' => 'Vermelho',
            'ano' => 2020,
        ]);

        $response = $this->postJson('/api/cliente/register', [
            'nome' => 'Outro Cliente',
            'cpf' => '99988877766',
            'rg' => '999888777',
            'endereco' => 'Rua X',
            'password' => '123456',
            'password_confirmation' => '123456',
            'veiculo' => [
                'placa' => 'ABC1234', // duplicada
                'modelo' => 'Gol',
                'cor' => 'Branco',
                'ano' => 2021,
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['veiculo.placa']);
    }
}
