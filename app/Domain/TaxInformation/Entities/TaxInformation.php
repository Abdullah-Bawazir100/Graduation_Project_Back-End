<?php

namespace App\Domain\TaxInformation\Entities;
class TaxInformation
{
    public function __construct(
        public ?int $id,
        public ?int $taxTypeId,
        public ?int $taxPayerId,
        public string $taxAmount,
        public string $lastPayment,
    )
    {}
}
