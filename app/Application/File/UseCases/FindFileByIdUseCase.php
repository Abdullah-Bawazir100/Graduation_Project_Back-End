<?php

namespace App\Application\File\UseCases;

use App\Domain\File\Entities\File;
use App\Domain\File\Repositories\FileRepositoryInterface;
use DomainException;

class FindFileByIdUseCase
{
    public function __construct(
        private FileRepositoryInterface $file_repository
    ) {}

    public function execute(int $id): ?File
    {
        $file = $this->file_repository->findById($id);
        if(!$file)
        {
            throw new DomainException("الملف مع ال ID [$id] غير موجود");
        }
        return $file;
    }
}
