<?php

namespace App\Repositories;

use App\Domain\Cliente;

interface ClienteRepository
{
    public function Save(Cliente $cliente): Cliente;
    public function Get(Criteria $criteria): ?Cliente;
}
