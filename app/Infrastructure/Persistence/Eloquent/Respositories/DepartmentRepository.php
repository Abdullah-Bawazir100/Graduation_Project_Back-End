<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Departments\Entities\Department;
use App\Domain\Department\ValueObjects\DepartmentName;
use App\Domain\Departments\Repositories\DepartmentRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\DepartmentModel;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    private function extractName(Department $department): string
    {
        return $department->name instanceof DepartmentName
            ? $department->name->value()
            : $department->name;
    }
    public function create(Department $department)
    {
         $departmentModel = DepartmentModel::create([
            'name' => $this->extractName($department),
        ]);

        return new Department(
            $departmentModel->id,
            new DepartmentName($departmentModel->name)
        );
    }

    public function update(Department $department): Department
    {
        $departmentModel = DepartmentModel::findOrFail($department->id);

        $departmentModel->update([
            'name' => $department->name->value(), // تحويل VO إلى string عند التحديث
        ]);

        return new Department($departmentModel->id, new DepartmentName($departmentModel->name));
    }

    public function delete(int $id)
    {
        DepartmentModel::findOrFail($id)->delete();
    }

    public function findById(int $id)
    {
        $departmentModel = DepartmentModel::find($id);
        return $departmentModel ? new Department($departmentModel->id, $departmentModel->name) : null;
    }

    public function getAll()
    {
        return DepartmentModel::all()
        ->map(fn($departmentModel) => new Department($departmentModel->id, $departmentModel->name))
        ->toArray();
    }

    public function existsByName(string $name)
    {
        $value = $name instanceof DepartmentName ? $name->value() : $name;
        return DepartmentModel::where('name', $value)->exists();   
    }
}