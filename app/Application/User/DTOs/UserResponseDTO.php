<?php

namespace App\Application\User\DTOs;

class UserResponseDTO
{
    public function __construct(
        public int $id,
        public string $firstName,
        public string $lastName,
        public string $dateOfBirth,
        public string $idCard,
        public string $userName,
        public string $phone,
        public string $email,
        public string $password,
        public int $createdBy,
        public int $departmentId,
        public string $departmentName,
        public string $role
    ) {}
}