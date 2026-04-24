<?php

namespace App\Application\District\DTOs;

class DistrictDTOs
{
    public function __construct(
        public ?string $name,
        public ?int $regionID
    )
    {}
}
