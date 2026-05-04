<?php

namespace App\Domain\CharitableCompany\Entities;

class CharitableCompany
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $tax_payer_id,
        public string $byLawsCopy
    )
    {}
}
