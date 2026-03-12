<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;
use App\Application\User\DTOs\UserDTO;

class UpdateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private DepartmentRepositoryInterface $departmentRepository
    ) {}

    public function execute(User $actor, int $userId, UserDTO $dto): User
    {
        $existing = $this->userRepository->findById($userId);
        if (!$existing) throw new \DomainException("User not found.");

        $department = $this->departmentRepository->findById($dto->departmentId);
        if (!$department) throw new \DomainException("Department not found.");

        $updatedUser = new User(
            id: $existing->id,
            firstName: $dto->firstName,
            lastName: $dto->lastName,
            dateOfBirth: new \DateTime($dto->dateOfBirth),
            idCard: $dto->idCard,
            userName: $dto->userName,
            phone: $dto->phone,
            email: $dto->email,
            password: $existing->password, // لا نغير الباسورد هنا
            createdBy: $existing->createdBy,
            department: $department,
            role: UserRole::from($dto->role)
        );

        return $this->userRepository->update($updatedUser);
    }
}