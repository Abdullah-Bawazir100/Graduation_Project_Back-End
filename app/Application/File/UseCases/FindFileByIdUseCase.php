<?php

namespace App\Application\File\UseCases;

use App\Domain\File\Entities\File;
use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use DomainException;

class FindFileByIdUseCase
{
    public function __construct(
        private FileRepositoryInterface $file_repository,
        private UserRepositoryInterface $user_repository
    ) {}

    public function execute(int $id, int $authenticatedUserId): ?File
    {
        $file = $this->file_repository->findById($id);
        if(!$file)
        {
            throw new DomainException("الملف مع ال ID [$id] غير موجود");
        }

        $user = $this->user_repository->findById($authenticatedUserId);
        if ($user && $user->role !== UserRole::Admin) {
            if (!$user->department || $user->department->id !== $file->department->id) {
                throw new DomainException("لا تملك صلاحية عرض ملف في قسم لا تنتمي إليه.");
            }
        }

        return $file;
    }
}
