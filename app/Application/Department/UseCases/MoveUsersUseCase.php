<?php

namespace App\Application\Department\UseCases;

use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\Department\Entities\Department;

class MoveUsersUseCase
{
    public function __construct(
        private DepartmentRepositoryInterface $departmentRepository
    )
    {}

    public function execute(int $oldDepartmentId, int $newDepartmentId): void
    {
        
        $oldDepartment = $this->departmentRepository->findById($oldDepartmentId);
        if (!$oldDepartment) {
            throw new \DomainException("القسم القديم [$oldDepartmentId] غير موجود.");
        }

        $newDepartment = $this->departmentRepository->findById($newDepartmentId);
        if (!$newDepartment) {
            throw new \DomainException("القسم الجديد [$newDepartmentId] غير موجود.");
        }

        if ($oldDepartmentId === $newDepartmentId) {
            throw new \DomainException("لا يمكن نقل المستخدمين إلى نفس القسم.");
        }

        $this->departmentRepository->moveUsersToAnotherDepartment($oldDepartmentId, $newDepartmentId);
    }
}
