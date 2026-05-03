<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Department\Entities\Department;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\DepartmentModel;
use App\Infrastructure\Persistence\Eloquent\Models\UserModel;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    public function create(Department $department): Department
    {
        $departmentModel = DepartmentModel::create([
            'name' => $department->name,
        ]);

        return new Department(
            $departmentModel->id,
            $departmentModel->name
        );
    }

    public function update(Department $department): Department
    {
        $departmentModel = DepartmentModel::find($department->id);

        $departmentModel->name = $department->name;
        $departmentModel->save();

        return new Department(
            $departmentModel->id,
            $departmentModel->name
        );
    }

    public function delete(int $id): void
    {
        DepartmentModel::findOrFail($id)->delete();
    }

    public function findById(int $id): ?Department
    {
        $departmentModel = DepartmentModel::find($id);

        if (!$departmentModel) {
            return null;
        }

        return new Department(
            $departmentModel->id,
            $departmentModel->name
        );
    }

    public function getAll(): array
    {
        return DepartmentModel::all()
            ->map(fn ($departmentModel) =>
                new Department(
                    $departmentModel->id,
                    $departmentModel->name
                )
            )
            ->toArray();
    }

    public function existsByName(string $name): bool
    {
        return DepartmentModel::where('name', $name)->exists();
    }

    public function countDepartments(): int
    {
        return DepartmentModel::count();
    }

    public function moveUsersToAnotherDepartment(int $oldDepartmentId, int $newDepartmentId): void
    {
        // Update all users from old department to new department
        UserModel::where('department_id', $oldDepartmentId)->update(['department_id' => $newDepartmentId]);
    }
}
