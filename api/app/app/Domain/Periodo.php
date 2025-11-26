<?php

namespace App\Domain;

use DomainException;
use Illuminate\Support\Carbon;

class Periodo
{
    private Carbon $comeco;

    private ?Carbon $fim;

    public function __construct(Carbon $comeco, ?Carbon $fim)
    {
        $this->comeco = $comeco;
        $this->fim = $fim;
    }

    public function GetComeco()
    {
        return $this->comeco;
    }

    public function GetFim()
    {
        return $this->fim;
    }

    public function GetPeriodo()
    {
        if (!$this->fim) throw new DomainException("Precisa da hora final para ter o perido");

        return $this->fim->getTimestamp() - $this->comeco->getTimestamp();
    }
}
