<?php

namespace App\Application\User\DTOs;

use DateTime;
class UserDTO
{
        public function __construct(
        public ?int $id = null,
        public string $firstName,
        public string $lastName,
        public ?DateTime $dateOfBirth = null,
        public ?string $idCard = null,
        public string $userName,
        public ?string $phone = null,
        public ?string $email = null,
        public string $password,
        public int $createdBy,
        public int $departmentID,
        public ?string $role = null,
        public bool $mustChangePassword = true
    ) {}
}