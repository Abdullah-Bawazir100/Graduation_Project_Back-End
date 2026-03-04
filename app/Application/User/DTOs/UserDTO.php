<?php

namespace App\Application\User\DTOs;

class UserDTO
{
    public function __construct(
        public ?int $id = null,
        public string $firstName,
        public string $lastName,
        public string $dateOfBirth,
        public string $idCard,
        public string $userName,
        public string $phone,
        public string $email,
        public string $password,
        public int $departmentId,
        public string $role
    ) {}
}