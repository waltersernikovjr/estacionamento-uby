<?php

namespace App\Domain;

class Veiculo
{
    private ?int $id;

    public readonly int $clientId;

    public readonly string $placa;

    public readonly string $modelo;

    public readonly string $cor;

    public readonly string $ano;

    public function __construct(int $clientId, string $placa, string $modelo, string $cor, string $ano, ?int $id)
    {
        $this->clientId = $clientId;
        $this->placa = $placa;
        $this->modelo = $modelo;
        $this->cor = $cor;
        $this->ano = $ano;
        $this->id = $id;
    }

    static function Create(int $clientId, $input)
    {
        return new Veiculo($clientId, $input->placa, $input->modelo, $input->cor, $input->ano, null);
    }

    public function SetId(int $id)
    {
        $this->id = $id;
    }

    public function GetId()
    {
        return $this->id;
    }
}
