<?php

declare(strict_types=1);

namespace App\Application\DTOs\Operator;

final readonly class UpdateOperatorDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $password = null,
        public ?string $phone = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'phone' => $this->phone,
        ], fn($value) => $value !== null);
    }
}
