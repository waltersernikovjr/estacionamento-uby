<?php

namespace App\Domain;

class Email
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function GetValue(): string
    {
        return $this->value;
    }
}
