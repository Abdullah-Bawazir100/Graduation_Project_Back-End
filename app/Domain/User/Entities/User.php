<?php

namespace App\Domain\User\Entities;

use App\Domain\User\Enums\UserRole;
use App\Domain\Department\Entities\Department;

class User {

    public function __construct(
        public readonly ?int $id,
        public string $firstName,
        public string $lastName,
        public ?string $idCard,
        public string $userName,
        public ?string $phone,
        public ?string $image,
        public string $password,
        public ?int $createdBy,
        public Department $department,
        public UserRole $role,
        public ?bool $mustChangePassword,
    ) {}

    public function mustChangePassword(): bool
    {
        return $this->mustChangePassword;
    }

    public function toArray(): array
{
    return [
        'id' => $this->id,
        'firstName' => $this->firstName,
        'lastName' => $this->lastName,
        'idCard' => $this->idCard,
        'userName' => $this->userName,
        'phone' => $this->phone,
        'image' => $this->image,
        'createdBy' => $this->createdBy,
        'department' => $this->department,
        'role' => $this->role,
        'mustChangePassword' => $this->mustChangePassword,
    ];
}

    /*
    public function changePassword(string $newPassword): void
    {
        $this->password = $newPassword;
        $this->mustChangePassword = false;
    }
    */
}
