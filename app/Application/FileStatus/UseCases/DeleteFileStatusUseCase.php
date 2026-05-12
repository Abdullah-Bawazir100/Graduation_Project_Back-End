<?php

namespace App\Application\FileStatus\UseCases;

use App\Domain\FileStatus\Repositories\FileStatusRepositoryInterface;
use DomainException;

class DeleteFileStatusUseCase
{
    public function __construct(
        private FileStatusRepositoryInterface $file_status_repository
    )
    {}

    public function execute(int $id)
    {
        $fileStatus = $this->file_status_repository->findById($id);
        if(!$fileStatus)
        {
            throw new DomainException("لا يوجد حالة ملف مع ال ID [$id].");
        }
        return $this->file_status_repository->delete($id);
    }
}
