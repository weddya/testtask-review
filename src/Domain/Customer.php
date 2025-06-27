<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Domain;

final readonly class Customer
{
    public function __construct(
        private int $id,
        private string $firstName,
        private string $lastName,
        private string $middleName,
        private string $email,
    ) {
        if (empty($firstName)) {
            throw new \InvalidArgumentException('First name cannot be empty');
        }
        
        if (empty($lastName)) {
            throw new \InvalidArgumentException('Last name cannot be empty');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFullName(): string
    {
        $nameParts = array_filter([$this->lastName, $this->firstName, $this->middleName]);
        return implode(' ', $nameParts);
    }
}
