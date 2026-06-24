<?php

namespace App\Application\File\UseCases;

use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\TaxPayer\Enums\enFileType;

class CountFilesByTypeUseCase
{
    public function __construct(
        private FileRepositoryInterface $repository
    ) {}

    public function execute(enFileType $type, ?int $departmentId = null): int
    {
                echo "hhh";

        return $this->repository->countFilesByType($type, $departmentId);
    }
}
