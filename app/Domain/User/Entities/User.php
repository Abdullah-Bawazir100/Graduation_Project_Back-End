<?php 

namespace App\Domain\User\Entities;

use App\Domain\User\Enums\UserRole;
use App\Domain\Department\Entities\Department;

class User {

    public function __construct(
        public readonly ?int $id,
        public string $firstName,
        public string $lastName,
        public \DateTime $dateOfBirth,
        public string $idCard,
        public string $userName,
        public string $phone,
        public string $email,
        public string $password,
        public ?int $createdBy,
        public Department $department,
        public UserRole $role,
    ) {}

}