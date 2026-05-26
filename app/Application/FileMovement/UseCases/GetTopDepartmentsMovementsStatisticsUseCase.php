<?php

namespace App\Application\FileMovement\UseCases;

use App\Domain\FileMovement\Repositories\FileMovementRepositoryInterface;

class GetTopDepartmentsMovementsStatisticsUseCase
{
    public function __construct(
        private FileMovementRepositoryInterface $file_movement_repository
    )
    {}

    public function execute(?int $month = null, ?int $year = null): array
    {
        return $this->file_movement_repository->getTopDepartmentsMovementsPerDay($month, $year);
    }
}
