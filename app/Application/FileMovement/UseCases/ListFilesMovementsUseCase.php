<?php

namespace App\Application\FileMovement\UseCases;

use App\Domain\FileMovement\Repositories\FileMovementRepositoryInterface;

class ListFilesMovementsUseCase
{
    public function __construct(
        private FileMovementRepositoryInterface $file_movement_repository
    )
    {}

    public function execute()
    {
        $filesMovements = $this->file_movement_repository->getAll();
        return [
            'filesMovements' => $filesMovements
        ];
    }
}
