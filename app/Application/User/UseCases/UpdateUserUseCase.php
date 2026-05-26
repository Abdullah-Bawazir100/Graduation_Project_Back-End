<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;
use App\Application\User\DTOs\UserDTO;
use DomainException;

class UpdateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private DepartmentRepositoryInterface $departmentRepository
    ) {}

    public function execute(User $actor, int $userId, UserDTO $dto): User
    {
        // Check authorization: Admins & Manager can update any user, others can only update themselves
        $isAdmin = ($actor->role === UserRole::Admin);

        if(!$isAdmin && $dto->role === UserRole::Admin)
        {
            throw new DomainException("غير مصرح : لا يمكنك تحديث دور المستخدم الى أدمن.");
        }

        if (
            !$isAdmin &&
            $actor->department->id !== $dto->departmentID
        ) {
            throw new DomainException(
                'لا يمكنك تحديث مستخدمين لقسم غير القسم الذي تعمل فيه.'
            );
        }

        $existingUser = $this->userRepository->findById($userId);
        if (!$existingUser) throw new DomainException(' المستخدم مع ال ID [' . $userId . '] غير موجود.');

        $department = $this->departmentRepository->findById($dto->departmentID);
        if (!$department) throw new DomainException('القسم مع ال ID [' . $dto->departmentID . '] غير موجود.');

        $updatedUser = new User(
            id: $existingUser->id,
            firstName: $dto->firstName,
            lastName: $dto->lastName,
            idCard: $dto->idCard,
            userName: $dto->userName,
            phone: $dto->phone,
            image: $dto->image,
            password: $existingUser->password,
            createdBy: $existingUser->createdBy,
            department: $department,
            role: $dto->role,
            mustChangePassword: $existingUser->mustChangePassword
        );

        return $this->userRepository->update($updatedUser);
    }
}
