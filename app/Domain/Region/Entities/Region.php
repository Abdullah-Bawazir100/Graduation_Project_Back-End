<?php

namespace App\Domain\Region\Entities;

class Region
{
    public function __construct(
        public ?int $id,
        public string $name
    )
    {}
}
