<?php

declare(strict_types=1);

namespace App\Application\DTOs\Customer;

final readonly class UpdateCustomerDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $cpf = null,
        public ?string $password = null,
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
        return array_filter([
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
        ], fn($value) => $value !== null);
    }
}
