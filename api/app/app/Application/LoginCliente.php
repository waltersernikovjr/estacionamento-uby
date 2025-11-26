<?php

namespace App\Application;

use App\Repositories\ClienteRepository;
use App\Repositories\Criteria;
use DomainException;

class LoginCliente
{
    private ClienteRepository $cliente_repository;

    public function __construct(ClienteRepository $cliente_repository)
    {
        $this->cliente_repository = $cliente_repository;
    }

    public function execute($input)
    {
        $cliente = $this->cliente_repository->Get(new Criteria("email", $input->email));

        if (!$cliente) throw new DomainException("Autenticacao falou");

        $cliente->Login($input->password);

        return $cliente;
    }
}
