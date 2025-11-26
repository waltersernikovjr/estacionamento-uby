<?php

namespace App\Repositories;

use App\Domain\Veiculo;
use Illuminate\Support\Facades\DB;

class DatabaseVeiculoRepository implements VeiculoRepository
{
    public function Get(Criteria $criteria): ?Veiculo
    {
        $row = DB::table('veiculos')
            ->where($criteria->key, $criteria->value)
            ->first();

        if (!$row) {
            return null;
        }

        return new Veiculo(
            clientId: (int) $row->cliente_id,
            placa: $row->placa,
            modelo: $row->modelo,
            cor: $row->cor,
            ano: $row->ano,
            id: (int) $row->id
        );
    }

    public function Save(Veiculo $veiculo): Veiculo
    {
        $id = DB::table("veiculos")->insertGetId([
            'cliente_id' => $veiculo->clientId,
            'placa' => $veiculo->placa,
            'modelo' => $veiculo->modelo,
            'cor' => $veiculo->cor,
            'ano' => $veiculo->ano,
        ]);

        $veiculo->SetId($id);

        return $veiculo;
    }
}
