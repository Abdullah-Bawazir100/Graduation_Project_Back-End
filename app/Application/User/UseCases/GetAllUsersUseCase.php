<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Application\User\DTOs\UserResponseDTO;
use App\Domain\User\Entities\User;

class GetAllUsersUseCase
{
    public function __construct(private UserRepositoryInterface $repository) {}

    public function execute(User $actor, ?string $search = null): array
    {
        $actorRoleValue = $actor->role instanceof UserRole ? $actor->role->value : $actor->role;
        $isAdmin = $actorRoleValue === UserRole::Admin->value;

        $departmentId = $isAdmin ? null : (int)$actor->department->id;

        $users = $this->repository->getAll($search, $departmentId);

        if (!$isAdmin) {
            $users = array_filter($users, function(User $user) {
                $roleValue = $user->role instanceof UserRole ? $user->role->value : $user->role;
                return $roleValue !== UserRole::Admin->value;
            });
        }

        $mappedUsers = array_map(fn(User $user) => new UserResponseDTO(
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
            role: $user->role->value
        ), $users);


        return [
            'users' => array_values($mappedUsers),
        ];
    }
}
