<?php

namespace App\Application\FileMovement\UseCases;

use App\Domain\FileMovement\Repositories\FileMovementRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Enums\UserRole;

class ListFilesMovementsUseCase
{
    public function __construct(
        private FileMovementRepositoryInterface $file_movement_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(int $authenticatedUserId)
    {
        $user = $this->user_repository->findById($authenticatedUserId);

        $departmentId = null;
        if ($user && $user->role !== UserRole::Admin) {
            $departmentId = $user->department?->id;
        }

        $filesMovements = $this->file_movement_repository->getAll($departmentId);
        return [
            'statistics' => $this->file_movement_repository->countFileMovements($departmentId),
            'filesMovements' => $filesMovements
        ];
    }
}
