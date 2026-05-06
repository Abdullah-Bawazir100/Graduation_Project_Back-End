<?php

namespace App\Application\TaxType\DTOs;
class TaxTypeDTOs
{
    public function __construct(
        public readonly ?string $name,
    ) {}
}
