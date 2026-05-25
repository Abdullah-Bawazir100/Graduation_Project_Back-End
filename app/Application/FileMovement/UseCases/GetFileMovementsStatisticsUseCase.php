<?php

namespace App\Application\FileMovement\UseCases;

use App\Domain\FileMovement\Repositories\FileMovementRepositoryInterface;

class GetFileMovementsStatisticsUseCase
{
    public function __construct(
        private FileMovementRepositoryInterface $repository
    ) {}

    public function execute(): array
    {
        return $this->repository->getFileMovementsStatistics();
    }
}
