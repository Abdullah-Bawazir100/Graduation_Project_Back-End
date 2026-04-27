<?php

namespace App\Application\Department\UseCases;

use App\Domain\Department\Repositories\DepartmentRepositoryInterface;

class CountDepartmentsUseCase
{
    public function __construct(
        private DepartmentRepositoryInterface $departmentRepository
    ) {}

    public function execute(): int
    {
        return $this->departmentRepository->countDepartments();
    }
}