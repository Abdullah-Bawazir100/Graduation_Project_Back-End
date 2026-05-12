<?php

namespace App\Application\FileStatus\UseCases;

use App\Domain\FileStatus\Repositories\FileStatusRepositoryInterface;

class ListFileStatusUseCase
{
    public function __construct(
        private FileStatusRepositoryInterface $file_status_repository
    )
    {}

    public function execute()
    {
        return $this->file_status_repository->getAll();
    }
}
