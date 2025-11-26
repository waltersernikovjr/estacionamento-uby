<?php

namespace App\Repositories;

use App\Domain\Vaga;

interface VagaRepository
{
    public function Save(Vaga $vaga): Vaga;
    public function Get(Criteria $criteria): ?Vaga;
}
