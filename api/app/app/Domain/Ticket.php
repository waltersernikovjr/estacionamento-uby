<?php

namespace App\Domain;

use Illuminate\Support\Carbon;

class Ticket
{
    private ?int $id;

    public readonly int $veiculoId;

    private ?int $precoTotal;

    private Periodo $periodo;

    public function __construct(int $veiculoId, Periodo $periodo, ?int $precoTotal, ?int $id)
    {
        $this->veiculoId = $veiculoId;
        $this->periodo   = $periodo;
        $this->precoTotal = $precoTotal;
        $this->id = $id;
    }

    public function GetVeiculoId()
    {
        return $this->veiculoId;
    }

    public function GetPrecoTotal()
    {
        return $this->precoTotal;
    }

    public function GetComeco()
    {
        return $this->periodo->GetComeco();
    }

    public function GetFim()
    {
        return $this->periodo->GetFim();
    }

    public function GetId()
    {
        return $this->id;
    }

    public function SetId(int $id)
    {
        $this->id = $id;
    }

    public function Fechar(Vaga $vaga)
    {
        $this->periodo = new Periodo(
            $this->periodo->getComeco(),
            Carbon::now()
        );

        $minutosEstacionados = $this->periodo->GetPeriodo();

        if ($minutosEstacionados <= 0) {
            throw new \DomainException('Horário de saída deve ser posterior ao horário de entrada.');
        }

        $horasCobradas = (int) ceil($minutosEstacionados / (1000 * 60 * 60));

        $precoPorHora = $vaga->preco;

        $this->precoTotal = $horasCobradas * $precoPorHora;
    }
}
