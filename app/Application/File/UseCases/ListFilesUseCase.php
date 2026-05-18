<?php

namespace App\Application\File\UseCases;

use App\Domain\File\Repositories\FileRepositoryInterface;

class ListFilesUseCase
{
    public function __construct(
        private FileRepositoryInterface $file_repository,
    ){}

    public function execute()
    {
        $files = $this->file_repository->getAll();
        return $files;
    }
}
