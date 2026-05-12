<?php

namespace App\Application\FileStatus\UseCases;

use App\Application\FileStatus\DTOs\FileStatusDTOs;
use App\Domain\FileStatus\Entities\FileStatus;
use App\Domain\FileStatus\Repositories\FileStatusRepositoryInterface;
use DomainException;

class UpdateFileStatusUseCase
{
    public function __construct(private FileStatusRepositoryInterface $fileStatusRepository)
    {}

    public function execute(FileStatusDTOs $fileStatusDTOs , int $id): ?FileStatus
    {
        $existingFileStatus = $this->fileStatusRepository->findById($id);
        if(!$existingFileStatus)
        {
            throw new DomainException("لا يوجد حالة ملف مع ال ID [$id].");
        }

        $statusName = $fileStatusDTOs->statusName ?? $existingFileStatus->statusName;
        $statusDescription = $fileStatusDTOs->statusDescription ?? $existingFileStatus->statusDescription;

        return $this->fileStatusRepository->update(
            new FileStatus($id, $statusName , $statusDescription)
        );
    }
}
