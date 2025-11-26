<?php

namespace App\Domain;

class DocumentoCliente
{
    private RG $rg;

    private CPF $cpf;

    public function __construct(string $cpf, string $rg)
    {
        $this->rg = new RG($rg);
        $this->cpf = CPF::Create($cpf);
    }

    public function GetCPF()
    {
        return $this->cpf->GetValue();
    }

    public function GetRG()
    {
        return $this->rg->GetValue();
    }
}
