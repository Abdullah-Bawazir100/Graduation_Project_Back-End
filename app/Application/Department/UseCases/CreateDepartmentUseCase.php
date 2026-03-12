<?php

namespace App\Application\Department\UseCases;

use App\Application\Department\DTOs\DepartmentDTO;
use App\Domain\Department\Entities\Department;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;

class CreateDepartmentUseCase
{
    private DepartmentRepositoryInterface $departmentRepository;

    public function __construct(DepartmentRepositoryInterface $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    public function execute(DepartmentDTO $departmentDTO): Department
    {
        $name = trim($departmentDTO->name);

        // Business Rule: منع التكرار
        if ($this->departmentRepository->existsByName($name)) {
            throw new \DomainException("Department with name '{$name}' already exists.");
        }

        return $this->departmentRepository->create(
            new Department(null, $name)
        );
    }
}