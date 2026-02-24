<?php 

namespace App\Application\Department\DTOs;

class DepartmentDTO
{
    public function __construct(
        public readonly ?string $name,
    ) {}
}