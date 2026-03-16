<?php

namespace App\Application\User\DTOs;

use DateTime;
class UserDTO
{
        public function __construct (
            public ?int $id,
            public string $firstName,
            public string $lastName,
            public ?DateTime $dateOfBirth = null,
            public ?string $idCard = null,
            public ?string $userName = null,
            public ?string $password = null,
            public ?string $phone = null,
            public int $departmentID,
            public int $createdBy,
            public ?string $role = null,
            public bool $mustChangePassword = false
    ) {}
}
