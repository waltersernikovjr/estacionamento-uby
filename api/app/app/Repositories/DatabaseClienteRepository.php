<?php

namespace App\Repositories;

use App\Domain\Cliente;
use App\Domain\Email;
use App\Domain\DocumentoCliente;
use App\Domain\Password;
use Illuminate\Support\Facades\DB;


class DatabaseClienteRepository implements ClienteRepository
{
    public function Get(Criteria $criteria): ?Cliente
    {
        $data = DB::table("clientes")->where($criteria->key, $criteria->value)->first();

        if (!$data) return null;

        return new Cliente($data->nome, $data->endereco, new Email($data->email), new DocumentoCliente($data->cpf, $data->rg), new Password($data->password), $data->id);
    }

    public function Save(Cliente $cliente): Cliente
    {
        $id = DB::table("clientes")->insertGetId([
            'nome' => $cliente->GetNome(),
            'email' => $cliente->GetEmail(),
            'cpf' => $cliente->GetCPF(),
            'rg' => $cliente->GetRG(),
            'endereco' => $cliente->GetEndereco(),
            'password' => $cliente->GetPassword(),
        ]);

        $cliente->SetId($id);

        return $cliente;
    }
}
