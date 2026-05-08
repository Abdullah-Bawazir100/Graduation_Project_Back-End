<?php

namespace App\Application\TaxInformation\DTOs;

use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\TaxType\Entities\TaxType;

class TaxInformationDTOs
{
    public function __construct(
        public ?int $id,
        public string $taxAmount,
        public string $lastPayment,
        public ?int $taxTypeId,
        public ?int $taxPayerId,
    ){}
}
