<?php

namespace App\Application\Department\UseCases;

use App\Domain\Department\Repositories\DepartmentRepositoryInterface;

class GetDepartmentsStatisticsUseCase
{
    public function __construct(
        private DepartmentRepositoryInterface $repository
    ) {}

    public function execute(): array
    {
        return $this->repository->getDepartmentsWithStatistics();
    }
}
