<?php

namespace App\Application\User\DTOs;

use DateTime;
class UserDTO
{
        public function __construct(
        public string $firstName,
        public string $lastName,
        public ?DateTime $dateOfBirth = null,
        public ?string $idCard = null,
        public ?string $userName = null,
        public ?string $phone = null,
        public int $departmentID,
        public ?string $role = null,
    ) {}
}