<?php

namespace App\Application\User\DTOs;

use App\Domain\User\Enums\UserRole;
use DateTime;
class UserDTO
{
        public function __construct (
            public ?int $id,
            public ?string $firstName,
            public ?string $lastName,
            public ?DateTime $dateOfBirth = null,
            public ?string $idCard = null,
            public ?string $userName = null,
            public ?string $password = null,
            public ?string $phone = null,
            public ?string $image = null,
            public int $departmentID,
            public int $createdBy,
            public $role = null,
    ) {}

    public function getRole(): UserRole
    {
        if ($this->role instanceof UserRole) {
            return $this->role;
        }

        if ($this->role !== null) {
            return UserRole::from($this->role);
        }

        return UserRole::Employee;
    }
}
