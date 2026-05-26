<?php

namespace App\Application\Department\UseCases;

use App\Application\Department\DTOs\DepartmentDTO;
use App\Domain\Department\Entities\Department;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;

class UpdateDepartmentUseCase
{
    public function __construct(
        private DepartmentRepositoryInterface $departmentRepository
    ) {}

    public function execute(User $actor, int $id, DepartmentDTO $departmentDTO): Department
    {
        $isAdmin = $actor->role === UserRole::Admin;

        // Non-admin can only update their own department
        if (!$isAdmin && (int)$actor->department->id !== $id) {
            throw new \DomainException('غير مصرح لك بتعديل قسم غير القسم الذي تعمل فيه.');
        }

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