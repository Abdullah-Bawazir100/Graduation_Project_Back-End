<?php

namespace App\Application\Address\DTOs;

class AddressDTOs
{
    public function __construct(
        public ?int $regionID,
        public ?int $districtID
    )
    {}
}
