<?php

namespace App\Application\Department\UseCases;

use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\Department\Entities\Department;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;
use DomainException;

class ShowDepartmentUseCase
{
    public function __construct(private DepartmentRepositoryInterface $departmentRepository) {}

    public function execute(User $actor , int $id): ?Department
    {
        $department = $this->departmentRepository->findById($id);
        $isAdmin = $actor->role === UserRole::Admin;

        if (!$department) {
            throw new DomainException("القسم مع ال ID [{$id}] غير موجود.");
        }

        if (!$isAdmin && (int)$actor->department->id !== $id) {
            throw new DomainException("ليس لديك صلاحية عرض قسم لا تعمل فيه.");
        }

        return $department;
    }
}
