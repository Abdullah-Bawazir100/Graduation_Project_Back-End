<?php

namespace App\Application\Department\UseCases;

use App\Application\Department\DTOs\DepartmentDTO;
use App\Domain\Departments\Entities\Department;
use App\Domain\Department\ValueObjects\DepartmentName;
use App\Domain\Departments\Repositories\DepartmentRepositoryInterface;


class UpdateDepartmentUseCase
{
    public function __construct(private DepartmentRepositoryInterface $departmentRepository) {}

    public function execute(int $id, DepartmentDTO $departmentDTO)
    {
        $nameVO = new DepartmentName($departmentDTO->name);
        return $this->departmentRepository->update(new Department($id, $nameVO));
    }
}