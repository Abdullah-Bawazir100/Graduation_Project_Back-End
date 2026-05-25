<?php

namespace App\Domain\Department\Repositories;
use App\Domain\Department\Entities\Department;

interface DepartmentRepositoryInterface
{
    public function create(Department $department);
    public function update(Department $department);
    public function delete(int $id);
    public function findById(int $id);
    public function getAll();
    public function existsByName(string $name);
    public function moveUsersToAnotherDepartment(int $oldDepartmentId, int $newDepartmentId);
    public function countDepartments(): int;
    public function getDepartmentsWithStatistics(): array;
}