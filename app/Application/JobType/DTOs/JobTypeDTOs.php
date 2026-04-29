<?php

namespace App\Application\JobType\DTOs;

class JobTypeDTOs
{
    public function __construct(
        public readonly ?string $name,
    ) {}
}
