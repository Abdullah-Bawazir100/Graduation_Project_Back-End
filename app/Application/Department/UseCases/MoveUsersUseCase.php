<?php

namespace App\Application\Department\UseCases;

use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;

class MoveUsersUseCase
{
    public function __construct(
        private DepartmentRepositoryInterface $departmentRepository,
        private UserRepositoryInterface $userRepository,
    )
    {}

    public function execute(User $actor, int $oldDepartmentId, int $newDepartmentId): void
    {
        $isAdmin = $actor->role === UserRole::Admin;

        // Non-admin can only move users to their own department
        if (!$isAdmin && (int)$actor->department->id !== $newDepartmentId) {
            throw new \DomainException('غير مصرح لك بنقل المستخدمين إلى قسم غير القسم الذي تعمل فيه.');
        }

        // Non-admin cannot move admin users
        if (!$isAdmin && $this->userRepository->hasAdminInDepartment($oldDepartmentId)) {
            throw new \DomainException('لا يمكنك نقل مستخدمين من قسم يحتوي على مستخدم أدمن.');
        }

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
