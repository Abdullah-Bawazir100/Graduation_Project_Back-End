<?php

namespace App\Application\FileMovement\UseCases;

use App\Domain\FileMovement\Repositories\FileMovementRepositoryInterface;

class CountFileMovementsUseCase
{
    public function __construct(
        private FileMovementRepositoryInterface $repository
    ) {}

    public function execute(): int
    {
        return $this->repository->getFileMovementCount();
    }
}
