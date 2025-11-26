<?php

namespace App\Application;

use App\Domain\Cliente;
use App\Domain\Veiculo;
use App\Repositories\ClienteRepository;
use App\Repositories\Criteria;
use App\Repositories\VeiculoRepository;
use DomainException;

class CreateCliente
{
    private ClienteRepository $cliente_repository;
    private VeiculoRepository $veiculo_repository;

    public function __construct(ClienteRepository $cliente_repository, VeiculoRepository $veiculo_repository)
    {
        $this->cliente_repository = $cliente_repository;
        $this->veiculo_repository = $veiculo_repository;
    }

    public function execute($input)
    {
        $existing_cliente = $this->cliente_repository->Get(new Criteria("email", $input->email));

        if ($existing_cliente) throw new DomainException("Email ja cadastrado");

        $cliente = Cliente::Create($input);

        $saved_cliente = $this->cliente_repository->Save($cliente);

        $this->veiculo_repository->Save(Veiculo::Create($saved_cliente->GetId(), (object) $input['veiculo']));

        return $saved_cliente;
    }
}
