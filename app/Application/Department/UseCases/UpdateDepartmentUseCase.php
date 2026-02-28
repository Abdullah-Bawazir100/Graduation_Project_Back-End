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
            throw new \Exception("Department with ID [$id] not found.");
        }

        $name = trim($departmentDTO->name);

        // Business Rule: منع تكرار الاسم (باستثناء نفس القسم)
        if (
            $name !== $department->name &&
            $this->departmentRepository->existsByName($name)
        ) {
            throw new \DomainException("Department with name '{$name}' already exists.");
        }

        return $this->departmentRepository->update(
            new Department($id, $name)
        );
    }
}