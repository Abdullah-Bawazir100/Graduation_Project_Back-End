<?php

namespace App\Domain\Departments\Entities;

use App\Domain\Department\ValueObjects\DepartmentName;

class Department
{
    public function __construct(
        public ?int $id,
        public DepartmentName $name,
    ) {}
}