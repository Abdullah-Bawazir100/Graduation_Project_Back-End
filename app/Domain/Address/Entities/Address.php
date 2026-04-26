<?php

namespace App\Domain\Address\Entities;

use App\Domain\District\Entities\District;
use App\Domain\Region\Entities\Region;

class Address
{
    public function __construct(
        public ?int $id,
        public string $address,
        public Region $region,
        public District $district
    )
    {}
}