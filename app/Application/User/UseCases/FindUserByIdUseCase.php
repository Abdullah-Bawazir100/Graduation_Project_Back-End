<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Application\User\DTOs\UserResponseDTO;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;

class FindUserByIdUseCase
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function execute(User $actor, int $id): ?UserResponseDTO
    {
        $user = $this->repository->findById($id);
        if (!$user) return null;

        $actorRoleValue = $actor->role instanceof UserRole ? $actor->role->value : $actor->role;
        $isAdmin = $actorRoleValue === UserRole::Admin->value;

        if (!$isAdmin && (int)$user->department->id !== (int)$actor->department->id) {
            throw new \DomainException('غير مصرح لك بعرض بيانات مستخدم من قسم آخر.');
        }

        return new UserResponseDTO(
            id: $user->id,
            firstName: $user->firstName,
            lastName: $user->lastName,
            idCard: $user->idCard,
            userName: $user->userName,
            phone: $user->phone,
            image: $user->image,
            createdBy: $user->createdBy ?? 0,
            departmentID: $user->department->id,
            departmentName: $user->department->name,
            role: $user->role->value,
        );
    }
}
