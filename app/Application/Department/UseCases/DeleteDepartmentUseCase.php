<?php

namespace App\Application\Department\UseCases;

use App\Domain\Departments\Repositories\DepartmentRepositoryInterface;
use App\Domain\Departments\Entities\Department;

class DeleteDepartmentUseCase
{
    public function __construct(private DepartmentRepositoryInterface $departmentRepository)
    {
    }

    public function execute(int $id): void
    {
        $this->departmentRepository->delete($id);
    }
}

