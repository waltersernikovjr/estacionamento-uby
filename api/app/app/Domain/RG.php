<?php

namespace App\Domain;

class RG
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    static function Create(string $value)
    {
        return new RG($value);
    }

    public function GetValue()
    {
        return $this->value;
    }
}
