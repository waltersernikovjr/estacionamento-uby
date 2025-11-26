<?php

namespace App\Application;

use App\Repositories\Criteria;
use App\Repositories\VagaRepository;
use App\Repositories\VeiculoRepository;
use DomainException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class UpdateVaga
{
    public function __construct(protected VagaRepository $vaga_repository, protected VeiculoRepository $veiculo_repository) {}

    public function execute($input)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $veiculo = $this->veiculo_repository->Get(new Criteria("cliente_id", $user->id));

        if (!$veiculo) throw new DomainException("Cliente sem veiculo");

        $vaga = $this->vaga_repository->Get(new Criteria("id", $input));

        if (!$vaga) throw new DomainException("Vaga nao encotrada");

        if ($vaga->GetDisponivel()) {
            $vaga->Ocupar($veiculo);
        } else {
            $vaga->Liberar($veiculo);
        }

        $this->vaga_repository->Save($vaga);
    }
}
