<?php

namespace App\Application\User\DTOs;

class UserResponseDTO
{
    public function __construct(
        public ?int $id,
        public string $firstName,
        public string $lastName,
        public ?\DateTime $dateOfBirth,
        public ?string $idCard,
        public string $userName,
        public ?string $phone,
        public ?string $image,
        public int $createdBy,
        public int $departmentID,
        public string $departmentName,
        public string $role
    ) {}
}
