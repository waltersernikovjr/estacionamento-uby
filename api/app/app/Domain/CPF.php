<?php

namespace App\Domain;

use DomainException;

class CPF
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    static function Create(string $value)
    {
        if (strlen($value) !== 14) throw new DomainException("CPF fora do formato");

        return new CPF($value);
    }

    public function GetValue()
    {
        return $this->value;
    }
}
