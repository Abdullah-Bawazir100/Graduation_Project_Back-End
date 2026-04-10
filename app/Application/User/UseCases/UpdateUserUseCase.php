<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;
use App\Application\User\DTOs\UserDTO;
use DateTime;

class UpdateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private DepartmentRepositoryInterface $departmentRepository
    ) {}

    public function execute(User $actor, int $userId, UserDTO $dto): User
    {
        $existingUser = $this->userRepository->findById($userId);
        if (!$existingUser) throw new \DomainException("User not found.");

        $department = $this->departmentRepository->findById($dto->departmentID);
        if (!$department) throw new \DomainException("Department not found.");

        $updatedUser = new User(
            id: $existingUser->id,
            firstName: $dto->firstName,
            lastName: $dto->lastName,
            dateOfBirth: $dto->dateOfBirth,
            idCard: $dto->idCard,
            userName: $dto->userName,
            phone: $dto->phone,
            image: $dto->image,
            password: $existingUser->password,
            createdBy: $existingUser->createdBy,
            department: $department,
            role: UserRole::from($dto->role)
        );

        return $this->userRepository->update($updatedUser);
    }
}
