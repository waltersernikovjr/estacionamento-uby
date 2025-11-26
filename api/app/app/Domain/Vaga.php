<?php

namespace App\Domain;

use Illuminate\Support\Carbon;
use DomainException;

class Vaga
{
    private ?int $id;

    public readonly string $numero;

    public readonly int $preco;

    private Dimensao $dimensao;

    private bool $disponivel;

    private ?Ticket $ticket;

    public function __construct(
        string   $numero,
        int      $precoEmCentavos,
        Dimensao $dimensao,
        bool     $disponivel,
        ?Ticket  $ticket,
        ?int     $id = null
    ) {
        $this->numero   = $numero;
        $this->preco    = $precoEmCentavos;
        $this->dimensao = $dimensao;
        $this->disponivel =  $disponivel;
        $this->ticket = $ticket;
        $this->id       = $id;
    }

    public function GetId()
    {
        return $this->id;
    }

    public function GetDisponivel()
    {
        return $this->disponivel;
    }

    public function GetTicket()
    {
        return $this->ticket;
    }

    public function SetId(int $id)
    {
        $this->id = $id;
    }

    public function GetLargura()
    {
        return $this->dimensao->GetLargura();
    }

    public function GetComprimento()
    {
        return $this->dimensao->GetComprimento();
    }

    public function Ocupar(Veiculo $veiculo)
    {
        $this->disponivel = false;

        $periodo = new Periodo(Carbon::now(), null);
        $this->ticket = new Ticket($veiculo->GetId(), $periodo, null, null);
    }

    public function Liberar(Veiculo $veiculo)
    {
        if ($this->ticket->GetVeiculoId() != $veiculo->GetId()) throw new DomainException("Nao pode libera vaga ocupada por outro veiculo");

        $this->ticket->Fechar($this);

        $this->disponivel = true;
    }
}
