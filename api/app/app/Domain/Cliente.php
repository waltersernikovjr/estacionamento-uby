<?php

namespace App\Domain;

use DomainException;
use Illuminate\Support\Facades\Auth;

class Cliente
{
    private ?int $id;

    private string $nome;

    private Email $email;

    private DocumentoCliente $documento;

    private string $endereco;

    private Password $password;

    public function __construct(string $nome, string $endereco, Email $email, DocumentoCliente $documento, Password $password, ?int $id)
    {
        $this->nome = $nome;
        $this->endereco = $endereco;
        $this->email = $email;
        $this->documento = $documento;
        $this->password = $password;
        $this->id = $id;
    }

    static function Create($input)
    {
        return new Cliente(
            $input->nome,
            $input->endereco,
            new Email($input->email),
            new DocumentoCliente($input->cpf, $input->rg),
            Password::Create($input->password),
            null
        );
    }

    public function SetId(int $id)
    {
        $this->id = $id;
    }

    public function GetId()
    {
        return $this->id;
    }

    public function GetNome()
    {
        return $this->nome;
    }

    public function GetEmail()
    {
        return $this->email->GetValue();
    }

    public function GetCPF()
    {
        return $this->documento->GetCPF();
    }

    public function GetRG()
    {
        return $this->documento->GetRG();
    }

    public function GetEndereco()
    {
        return $this->endereco;
    }

    public function GetPassword(): string
    {
        return $this->password->GetValue();
    }

    public function Login(string $password)
    {
        if (!$this->password->Compare($password)) throw new DomainException("Autenticao falhou");
    }
}
