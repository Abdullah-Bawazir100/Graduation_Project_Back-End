<?php

namespace App\Application\Department\UseCases;

use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\Department\Entities\Department;

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

