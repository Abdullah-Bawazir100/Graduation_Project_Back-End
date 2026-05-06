<?php

namespace App\Domain\TaxType\Entities;
class TaxType
{
    public function __construct(
        public ?int $id,
        public string $name,
    )
    {}
}
