<?php

namespace App\Application\File\UseCases;

use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Enums\UserRole;

class ListFilesUseCase
{
    public function __construct(
        private FileRepositoryInterface $file_repository,
        private UserRepositoryInterface $user_repository
    ){}

    public function execute(?string $search , int $authenticatedUserId)
    {
        $user = $this->user_repository->findById($authenticatedUserId);

        $departmentId = null;
        if ($user && $user->role !== UserRole::Admin) {
            $departmentId = $user->department?->id;
        }

        $files = $this->file_repository->getAll($search, $departmentId);
        return $files;
    }
}
