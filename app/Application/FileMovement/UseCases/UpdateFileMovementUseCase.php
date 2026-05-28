<?php

namespace App\Application\FileMovement\UseCases;

use App\Application\FileMovement\DTOs\FileMovementDTOs;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\FileMovement\Entities\FileMovement;
use App\Domain\FileMovement\Repositories\FileMovementRepositoryInterface;
use App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use DomainException;

class UpdateFileMovementUseCase
{
    public function __construct(
        private FileMovementRepositoryInterface $file_movement_repository,
        private FileRepositoryInterface $file_repository,
        private TaxCollectorRepositoryInterface $tax_collector_repository,
        private DepartmentRepositoryInterface $department_repository,
        private UserRepositoryInterface $user_repository,
    )
    {}

    public function execute(FileMovementDTOs $dto , int $id, int $authenticatedUserId): ?FileMovement
    {
        $existingFileMovement = $this->file_movement_repository->findById($id);
        if (!$existingFileMovement) {
            throw new DomainException("لا يوجد  حركة ملف مع ال ID [$id].");
        }

        $user = $this->user_repository->findById($authenticatedUserId);
        if (!$user) {
            throw new DomainException("المستخدم غير موجود.");
        }

        // الأدمن يستطيع تحديث أي حركة ملف ونقلها لأي قسم
        // غير الأدمن يحدث فقط حركات ملفات قسمه ولا ينقلها لقسم آخر
        if ($user->role !== UserRole::Admin) {
            if (!$user->department || $user->department->id !== $existingFileMovement->department->id) {
                throw new DomainException("لا يمكنك تعديل حركة ملف في قسم لا تنتمي إليه.");
            }

            if ($dto->departmentId !== null && $dto->departmentId !== $user->department->id) {
                throw new DomainException("لا يمكنك نقل حركة الملف إلى قسم لا تنتمي إليه.");
            }
        }

        $newFile = $existingFileMovement->file;
        if ($dto->fileId !== null) {
            $newFile = $this->file_repository->findById($dto->fileId);

            if (!$newFile) {
                throw new DomainException("لا يوجد ملف مع ال ID [$dto->fileId].");
            }
        }

        $newTaxCollector = $existingFileMovement->taxCollector;
        if ($dto->taxCollectorId !== null) {
            $newTaxCollector = $this->tax_collector_repository->findById($dto->taxCollectorId);

            if (!$newTaxCollector) {
                throw new DomainException("لا يوجد مأمور مع ال ID [$dto->taxCollectorId].");
            }
        }

        $newDepartment = $existingFileMovement->department;
        if ($dto->departmentId !== null) {
            $newDepartment = $this->department_repository->findById($dto->departmentId);

            if (!$newDepartment) {
                throw new DomainException("لا يوجد قسم مع ال ID [$dto->departmentId].");
            }
        }


        $fileMovement = new FileMovement(
            id: $id,
            status: $dto->status ?? $existingFileMovement->status,
            date: $dto->getDateAsCarbon() ?? $existingFileMovement->date,

            file: $newFile,
            taxCollector: $newTaxCollector,
            department: $newDepartment,
            creator: $existingFileMovement->creator,
        );

        return $this->file_movement_repository->update($fileMovement, $id);
    }
}
