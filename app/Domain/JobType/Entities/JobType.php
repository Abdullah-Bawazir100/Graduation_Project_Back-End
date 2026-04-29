<?php

namespace App\Domain\JobType\Entities;

class JobType
{
    public function __construct(
        public ?int $id,
        public string $name,
    ) {}
}
