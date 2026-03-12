<?php

namespace App\Domain\Department\Entities;

class Department 
{   
     public function __construct(
        public ?int $id,
        public string $name,
    ) {}
}