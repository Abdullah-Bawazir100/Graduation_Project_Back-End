<?php 

namespace App\Application\Department\UseCases;

use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;

class ListDepartmentUseCase
{
    public function __construct(
        private DepartmentRepositoryInterface $departmentRepository
    ) {}

    public function execute(User $actor)
    {
        $isAdmin = $actor->role === UserRole::Admin;

        if ($isAdmin) {
            return $this->departmentRepository->getAll();
        }

        // Non-admin: return only the department the actor belongs to
        $department = $this->departmentRepository->findById($actor->department->id);

        return $department ? [$department] : [];
    }
}