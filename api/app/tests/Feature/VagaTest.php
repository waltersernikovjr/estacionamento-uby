<?php

namespace Tests\Feature;

use App\Models\Operador;
use App\Models\Vaga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class VagaTest extends TestCase
{
    use RefreshDatabase;

    protected $operador;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operador = Operador::factory()->create();
        $this->token = Auth::login($this->operador);
    }

    public function test_operador_pode_criar_vaga()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->postJson('/api/vagas', [
            'numero' => 'A01',
            'preco_por_hora' => 8.50,
            'largura' => 2.40,
            'comprimento' => 5.00,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('vagas', ['numero' => 'A01']);
    }

    public function test_nao_pode_criar_vaga_sem_autenticacao()
    {
        $response = $this->postJson('/api/vagas', [
            'numero' => 'B05',
            'preco_por_hora' => 10.00,
            'largura' => 2.50,
            'comprimento' => 5.50,
        ]);

        $response->assertStatus(401); // Unauthorized
    }

    public function test_cliente_nao_pode_criar_vaga()
    {
        $cliente = \App\Models\Cliente::factory()->create();
        $tokenCliente = Auth::login($cliente);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $tokenCliente",
        ])->postJson('/api/vagas', [
            'numero' => 'C10',
            'preco_por_hora' => 12.00,
            'largura' => 3.00,
            'comprimento' => 6.00,
        ]);

        $response->assertStatus(401); // Não tem permissão no guard operador
    }

    public function test_operador_pode_listar_vagas()
    {
        Vaga::factory()->count(5)->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->getJson('/api/vagas');

        $response->assertStatus(200)
            ->assertJsonCount(5);
    }
}
