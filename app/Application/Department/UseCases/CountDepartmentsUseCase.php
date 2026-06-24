<?php

namespace App\Application\Department\UseCases;

use App\Domain\Department\Repositories\DepartmentRepositoryInterface;

class CountDepartmentsUseCase
{
    public function __construct(
        private DepartmentRepositoryInterface $repository
    ) {}

    public function execute(): int
    {
                echo "hhh";

        return $this->repository->countDepartments();
    }
}
