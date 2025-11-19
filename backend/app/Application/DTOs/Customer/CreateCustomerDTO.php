<?php

declare(strict_types=1);

namespace App\Application\DTOs\Customer;

final readonly class CreateCustomerDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $cpf,
        public string $password,
        public ?string $phone = null,
        public ?string $street = null,
        public ?string $neighborhood = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $zip_code = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'cpf' => $this->cpf,
            'password' => $this->password,
            'phone' => $this->phone,
            'street' => $this->street,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
        ];
    }
}
