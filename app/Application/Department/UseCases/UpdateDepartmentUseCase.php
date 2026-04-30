<?php

namespace App\Application\Department\UseCases;

use App\Application\Department\DTOs\DepartmentDTO;
use App\Domain\Department\Entities\Department;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;

class UpdateDepartmentUseCase
{
    public function __construct(
        private DepartmentRepositoryInterface $departmentRepository
    ) {}

    public function execute(int $id, DepartmentDTO $departmentDTO): Department
    {
        $department = $this->departmentRepository->findById($id);

        if (!$department) {
            throw new \DomainException("القسم مع ال ID [{$id}] غير موجود.");
        }

        $name = $departmentDTO->name ?? $department->name;

        if (
            $name !== $department->name &&
            $this->departmentRepository->existsByName($name)
        ) {
            throw new \DomainException("القسم مع الأسم [{$name}] موجود بالفعل.");
        }

        return $this->departmentRepository->update(
            new Department($id, $name)
        );
    }
}