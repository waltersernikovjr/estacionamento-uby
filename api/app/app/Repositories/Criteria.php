<?php

namespace App\Repositories;

class Criteria
{
    public string $key;
    public $value;

    public function __construct(string $key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }
}
