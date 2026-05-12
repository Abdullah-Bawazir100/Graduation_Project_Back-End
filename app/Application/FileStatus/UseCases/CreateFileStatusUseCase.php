<?php

namespace App\Application\FileStatus\UseCases;

use App\Application\FileStatus\DTOs\FileStatusDTOs;
use App\Domain\FileStatus\Entities\FileStatus;
use App\Domain\FileStatus\Repositories\FileStatusRepositoryInterface;

class CreateFileStatusUseCase
{
    public function __construct(
        private FileStatusRepositoryInterface $file_status_repository
    )
    {}

    public function execute(FileStatusDTOs $fileStatusDTOs)
    {
        $statusName = trim($fileStatusDTOs->statusName);
        $statusDescription = trim($fileStatusDTOs->statusDescription);


        return $this->file_status_repository->create(
            new FileStatus(
                id: null,
                statusName: $statusName,
                statusDescription: $statusDescription,
            )
        );
    }
}
