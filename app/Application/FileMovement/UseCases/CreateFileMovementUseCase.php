<?php

namespace App\Application\FileMovement\UseCases;

use App\Application\FileMovement\DTOs\FileMovementDTOs;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\FileMovement\Entities\FileMovement;
use App\Domain\FileMovement\Enums\enFileMovement;
use App\Domain\FileMovement\Repositories\FileMovementRepositoryInterface;
use App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class CreateFileMovementUseCase
{
    public function __construct(
        private FileMovementRepositoryInterface $file_movement_repository,
        private UserRepositoryInterface $user_repository,
        private TaxCollectorRepositoryInterface $tax_collector_repository,
        private FileRepositoryInterface $file_repository,
        private DepartmentRepositoryInterface $department_repository
    )
    {}

    public function execute(FileMovementDTOs $fileMovementDTOs , int $authenticatedUserId)
    {
        $creator = $this->user_repository->findById($authenticatedUserId);
        if(!$creator)
        {
            throw new DomainException("المستخدم الذي انشاء حركة الملف غير موجود.");
        }

        $file = $this->file_repository->findById($fileMovementDTOs->fileId);
        if(!$file)
        {
            throw new DomainException("لا يوجد ملف مع ال ID [$fileMovementDTOs->fileId].");
        }

        $taxCollector = $this->tax_collector_repository->findById($fileMovementDTOs->taxCollectorId);
        if(!$taxCollector)
        {
            throw new DomainException("لا يوجد مأمور مع ال ID [$fileMovementDTOs->taxCollectorId].");
        }

        $department = $this->department_repository->findById($fileMovementDTOs->departmentId);
        if(!$department)
        {
            throw new DomainException("لا يوجد قسم مع ال ID [$fileMovementDTOs->departmentId].");
        }

        if (
            $creator->role !== UserRole::Admin &&
            $creator->department->id !== $department->id) {
            throw new DomainException("لا يمكنك انشاء حركة ملف في قسم لا تعمل فيه.");
        }

        $lastMovement = $this->file_movement_repository
            ->findFileMovementByFileId($file->id);

        if ($lastMovement && ($lastMovement->status === enFileMovement::OutsideArchive ||
        $lastMovement->status === enFileMovement::Missing)) {
            throw new DomainException(
                "لا يمكن إنشاء حركة ملف لأن الملف خارج الأرشيف أو مفقود."
            );
        }

        $fileMovement = new FileMovement(
            id: null,
            status: $fileMovementDTOs->status,
            date: $fileMovementDTOs->getDateAsCarbon(),
            file: $file,
            taxCollector: $taxCollector,
            department: $department,
            creator: $creator
        );

        $createdFileMovement = $this->file_movement_repository->create($fileMovement);
        return [
            'fileMovementInfo' => $createdFileMovement,
        ];


    }
}
