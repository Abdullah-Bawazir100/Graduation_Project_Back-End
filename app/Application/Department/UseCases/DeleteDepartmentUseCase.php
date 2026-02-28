<?php

namespace App\Application\Department\UseCases;

use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\Department\Entities\Department;
use App\Http\Responses\ApiResponse;

class DeleteDepartmentUseCase
{
    public function __construct(private DepartmentRepositoryInterface $departmentRepository)
    {
    }

    public function execute(int $id): void
    {
        $department = $this->departmentRepository->findById($id);

        if (!$department) {
            throw new \DomainException("Department with ID [$id] not found.");
        }

        $this->departmentRepository->delete($id);
    }
}

