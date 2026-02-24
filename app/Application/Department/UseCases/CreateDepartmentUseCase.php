<?php

namespace App\Application\Department\UseCases;

use App\Application\Department\DTOs\DepartmentDTO;
use App\Domain\Department\ValueObjects\DepartmentName;
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
        // إنشاء Value Object من الاسم
        $nameVO = new DepartmentName($departmentDTO->name);

        // تحقق من وجود الاسم مسبقًا في قاعدة البيانات
        if ($this->departmentRepository->existsByName($nameVO->value())) {
            throw new \DomainException("Department with name '{$nameVO->value()}' already exists.");
        }

        // تمرير VO مباشرة إلى Entity
        return $this->departmentRepository->create(new Department(null, $nameVO));
    }
}