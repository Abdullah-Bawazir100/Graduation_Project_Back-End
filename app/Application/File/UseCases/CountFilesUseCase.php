<?php

namespace App\Application\File\UseCases;

use App\Domain\File\Repositories\FileRepositoryInterface;

class CountFilesUseCase
{
    public function __construct(
        private FileRepositoryInterface $repository
    ) {}

    public function execute(?int $departmentId = null): int
    {
        return $this->repository->countFiles($departmentId);
    }
}
