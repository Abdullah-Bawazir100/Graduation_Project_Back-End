<?php 

namespace App\Application\Department\UseCases;

use App\Domain\Departments\Repositories\DepartmentRepositoryInterface;
use App\Domain\Departments\Entities\Department;

class ListDepartmentUseCase
{
    public function __construct(
        private DepartmentRepositoryInterface $departmentRepository
    ) {}

    public function execute()
    {
        return $this->departmentRepository->getAll();
    }
}