<?php

namespace App\Repositories;

use App\Domain\Veiculo;

interface VeiculoRepository
{
    public function Get(Criteria $criteria): ?Veiculo;
    public function Save(Veiculo $veiculo): Veiculo;
}
