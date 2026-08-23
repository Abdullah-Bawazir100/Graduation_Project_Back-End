<?php

namespace App\Domain\CharitableCompany\Entities;

class CharitableCompany
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $taxPayerId,
        public string $byLawsCopy
    )
    {}
}
