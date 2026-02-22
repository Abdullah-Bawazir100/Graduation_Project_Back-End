<?php

namespace App\Domain\Departments\Repositories;
use App\Domain\Departments\Entities\Department;

interface DepartmentRepositoryInterface 
{
    public function create(Department $department);
    public function update(Department $department);
    public function delete(int $id);
    public function findById(int $id);
    public function getAll();
    public function existsByName(string $name);
}
