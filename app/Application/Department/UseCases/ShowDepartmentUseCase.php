<?php

namespace App\Application\Department\UseCases;

use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\Department\Entities\Department;

class ShowDepartmentUseCase
{
    public function __construct(private DepartmentRepositoryInterface $departmentRepository) {}

    public function execute(int $id): ?Department
    {
        $department = $this->departmentRepository->findById($id);

        if (!$department) {
            throw new \Exception("Department with ID [$id] not found.");
        }
        
        return $department;
    }
}