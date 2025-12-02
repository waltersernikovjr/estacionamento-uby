<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Infrastructure\Persistence\Models\Operator;
use App\Infrastructure\Persistence\Models\Customer;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔐 Criando usuários de teste...');

        Operator::updateOrCreate(
            ['email' => 'operador@uby.com'],
            [
                'name' => 'João Silva (Operador)',
                'cpf' => '123.456.789-00',
                'email' => 'operador@uby.com',
                'password' => Hash::make('senha123'),
                'email_verified_at' => now(),
            ]
        );

        Customer::updateOrCreate(
            ['email' => 'cliente@uby.com'],
            [
                'name' => 'Maria Santos (Cliente)',
                'cpf' => '987.654.321-00',
                'rg' => '12.345.678-9',
                'email' => 'cliente@uby.com',
                'password' => Hash::make('senha123'),
                'email_verified_at' => now(),
                'address_zipcode' => '37750-000',
                'address_street' => 'Rua Principal',
                'address_number' => '123',
                'address_complement' => 'Apto 101',
                'address_neighborhood' => 'Centro',
                'address_city' => 'Muzambinho',
                'address_state' => 'MG',
            ]
        );

        $this->command->info('');
        $this->command->info('✅ Usuários de teste criados com sucesso!');
        $this->command->info('');
        $this->command->table(
            ['Tipo', 'Nome', 'Email', 'Senha'],
            [
                ['Operador', 'João Silva', 'operador@uby.com', 'senha123'],
                ['Cliente', 'Maria Santos', 'cliente@uby.com', 'senha123'],
            ]
        );
        $this->command->info('');
        $this->command->info('🌐 URLs de acesso:');
        $this->command->info('   Frontend: http://localhost:3000');
        $this->command->info('   Backend API: http://localhost:8000/api/v1');
        $this->command->info('   Swagger Docs: http://localhost:8000/api/documentation');
    }
}
