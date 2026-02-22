<?php

namespace App\Application\Department\UseCases;

use App\Domain\Departments\Repositories\DepartmentRepositoryInterface;
use App\Domain\Departments\Entities\Department;

class ShowDepartmentUseCase
{
    public function __construct(private DepartmentRepositoryInterface $departmentRepository) {}

    public function execute(int $id): ?Department
    {
        return $this->departmentRepository->findById($id);
    }
}