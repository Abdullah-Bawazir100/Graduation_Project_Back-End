<?php

namespace App\Application\User\DTOs;

use App\Domain\User\Enums\UserRole;
class UserDTO
{
        public function __construct (
            public ?int $id,
            public ?string $firstName,
            public ?string $lastName,
            public ?string $idCard,
            public ?string $userName,
            public ?string $password,
            public ?string $phone,
            public ?string $image,
            public int $departmentID,
            public int $createdBy,
            public UserRole $role,
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
