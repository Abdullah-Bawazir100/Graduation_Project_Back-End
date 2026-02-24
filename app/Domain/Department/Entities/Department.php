<?php

namespace App\Domain\Department\Entities;

use App\Domain\Department\ValueObjects\DepartmentName;

class Department
{
    public function __construct(
        public ?int $id,
        public DepartmentName $name,
    ) {}
}