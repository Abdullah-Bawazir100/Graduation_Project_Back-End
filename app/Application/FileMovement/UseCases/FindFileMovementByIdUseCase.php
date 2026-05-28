<?php

namespace App\Application\FileMovement\UseCases;

use App\Domain\FileMovement\Repositories\FileMovementRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use DomainException;

class FindFileMovementByIdUseCase
{
    public function __construct(
        private FileMovementRepositoryInterface $file_movement_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(int $id, int $authenticatedUserId)
    {
        $fileMovement = $this->file_movement_repository->findById($id);
        if(!$fileMovement)
        {
            throw new DomainException("لا يوجد حركة ملف مع ال ID [$id].");
        }

        $user = $this->user_repository->findById($authenticatedUserId);

        // الأدمن يجلب أي حركة ملف من أي قسم
        // غير الأدمن يجلب فقط حركات ملفات قسمه
        if ($user && $user->role !== UserRole::Admin) {
            if (!$user->department || $user->department->id !== $fileMovement->department->id) {
                throw new DomainException("لا تملك صلاحية عرض حركة ملف في قسم لا تنتمي إليه.");
            }
        }

        return $fileMovement;
    }
}
