<?php

namespace App\Domain;

class Dimensao
{
    private int $lagura;

    private int $comprimento;

    public function __construct(int $lagura, int $comprimento)
    {
        $this->lagura = $lagura;
        $this->comprimento = $comprimento;
    }

    public function GetLargura()
    {
        return $this->lagura;
    }

    public function GetComprimento()
    {
        return $this->comprimento;
    }
}
