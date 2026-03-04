<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;
use App\Application\User\DTOs\UserDTO;

class CreateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private DepartmentRepositoryInterface $departmentRepository
    ) {}

    public function execute(User $actor, UserDTO $dto): User
    {
        // تحقق من وجود القسم
        $department = $this->departmentRepository->findById($dto->departmentId);
        if (!$department) throw new \DomainException("Department not found.");

        // إنشاء كائن Domain Entity
        $user = new User(
            id: null,
            firstName: $dto->firstName,
            lastName: $dto->lastName,
            dateOfBirth: new \DateTime($dto->dateOfBirth),
            idCard: $dto->idCard,
            userName: $dto->userName,
            phone: $dto->phone,
            email: $dto->email,
            password: $dto->password,
            createdBy: $actor->id,
            department: $department,
            role: UserRole::from($dto->role)
        );

        return $this->userRepository->create($user);
    }
}