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
        // Check authorization: Admins & Manager can update any user, non-admins & non-manager can only update themselves
        if (($actor->role !== UserRole::Admin || $actor->role !== UserRole::Manager) && $actor->id !== $userId) {
            throw new \DomainException('الوصول ممنوع : أنت لا تمتلك صلاحية تحديث بيانات المستخدمين الأخرين.');
        }

        $existingUser = $this->userRepository->findById($userId);
        if (!$existingUser) throw new \DomainException(' المستخدم مع ال ID [' . $userId . '] غير موجود.');

        $department = $this->departmentRepository->findById($dto->departmentID);
        if (!$department) throw new \DomainException('القسم مع ال ID [' . $dto->departmentID . '] غير موجود.');

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
            role: UserRole::from($dto->role)
        );

        return $this->userRepository->update($updatedUser);
    }
}
