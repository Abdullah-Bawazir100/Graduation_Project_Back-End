<?php

namespace App\Application\FileStatus\UseCases;

use App\Domain\FileStatus\Repositories\FileStatusRepositoryInterface;

class CountFileStatusUseCase
{
    public function __construct(
        private FileStatusRepositoryInterface $file_status_repository
    )
    {
    }

    public function execute(): int
    {
        return $this->file_status_repository->getFileStatusCount();
    }
}
