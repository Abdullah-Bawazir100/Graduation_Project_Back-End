<?php

namespace App\Application\Department\UseCases;

use App\Domain\Department\Repositories\DepartmentRepositoryInterface;

class DeleteDepartmentUseCase
{
    public function __construct(private DepartmentRepositoryInterface $departmentRepository)
    {
    }

    public function execute(int $id): void
    {
        $department = $this->departmentRepository->findById($id);

        if (!$department) {
            throw new \DomainException("القسم مع ال ID [{$id}] غير موجود.");
        }

        $this->departmentRepository->delete($id);
    }
}

