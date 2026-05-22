<?php

namespace App\Application\FileMovement\UseCases;

use App\Domain\FileMovement\Repositories\FileMovementRepositoryInterface;
use DomainException;

class FindFileMovementByIdUseCase
{
    public function __construct(
        private FileMovementRepositoryInterface $file_movement_repository
    )
    {}

    public function execute(int $id)
    {
        $fileMovement = $this->file_movement_repository->findById($id);
        if(!$fileMovement)
        {
            throw new DomainException("لا يوجد حركة ملف مع ال ID [$id].");
        }
        return $fileMovement;
    }
}
