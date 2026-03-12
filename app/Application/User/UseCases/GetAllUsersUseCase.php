<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Application\User\DTOs\UserResponseDTO;
use App\Domain\User\Entities\User;

class GetAllUsersUseCase
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function execute(): array
    {
        $users = $this->repository->getAll();

        return array_map(fn(User $user) => new UserResponseDTO(
            id: $user->id,
            firstName: $user->firstName,
            lastName: $user->lastName,
            dateOfBirth: $user->dateOfBirth->format('Y-m-d'),
            idCard: $user->idCard,
            userName: $user->userName,
            phone: $user->phone,
            email: $user->email,
            password: $user->password,
            createdBy: $user->createdBy ?? 0,
            departmentId: $user->department->id,
            departmentName: $user->department->name,
            role: $user->role->value
        ), $users);
    }
}