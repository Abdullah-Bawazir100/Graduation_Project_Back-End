<?php

namespace App\Domain\District\Entities;

use App\Domain\Region\Entities\Region;

class District
{
    public function __construct(
        public ?int $id,
        public string $name,
        public Region $region
    )
    {}
}
