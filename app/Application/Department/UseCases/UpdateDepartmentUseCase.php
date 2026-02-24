<?php

namespace App\Application\Department\UseCases;

use App\Application\Department\DTOs\DepartmentDTO;
use App\Domain\Department\Entities\Department;
use App\Domain\Department\ValueObjects\DepartmentName;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Http\Resources\Department\DepartmentResource;

class UpdateDepartmentUseCase
{
    public function __construct(private DepartmentRepositoryInterface $departmentRepository) {}

    public function execute(int $id, DepartmentDTO $departmentDTO)
    {
        $department = $this->departmentRepository->findById($id);

        if (!$department) {
            throw new \Exception("Department with ID [$id] not found.");
        }

        $nameVO = new DepartmentName($departmentDTO->name);
        return $this->departmentRepository->update(new Department($id, $nameVO));
    }
}