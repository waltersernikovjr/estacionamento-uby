<?php

namespace App\Domain;

use Illuminate\Support\Facades\Hash;

class Password
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    static function Create(string $value): Password
    {
        $hashed = Hash::make($value);

        return new Password($hashed);
    }

    public function GetValue(): string
    {
        return $this->value;
    }

    public function Compare(string $valueToCompare): bool
    {
        return Hash::check($valueToCompare, $this->value);
    }
}
