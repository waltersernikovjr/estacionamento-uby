<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;

use App\Models\Operador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperadorAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_operador_pode_se_cadastrar()
    {
        $response = $this->postJson('/api/operador/register', [
            'nome' => 'Admin Parking',
            'cpf' => '12345678901',
            'email' => 'admin@estacionamento.com',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'user']);

        $this->assertDatabaseHas('operadores', [
            'email' => 'admin@estacionamento.com',
            'cpf' => '12345678901'
        ]);
    }

    public function test_operador_pode_fazer_login()
    {
        Operador::factory()->create([
            'email' => 'admin@estacionamento.com',
            'password' => Hash::make('senha123'),
        ]);

        $response = $this->postJson('/api/operador/login', [
            'email' => 'admin@estacionamento.com',
            'password' => "senha123",
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token']);
    }

    public function test_operador_nao_pode_logar_com_senha_errada()
    {
        $operador = Operador::factory()->create([
            'email' => 'admin@estacionamento.com',
            'password' => bcrypt('senha123'),
        ]);

        $response = $this->postJson('/api/operador/login', [
            'email' => 'admin@estacionamento.com',
            'password' => 'errada',
        ]);

        $response->assertStatus(401);
    }
}
