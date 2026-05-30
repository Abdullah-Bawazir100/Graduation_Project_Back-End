<?php

namespace App\Domain\TaxInformation\Entities;

use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\TaxType\Entities\TaxType;

class TaxInformation
{
    public function __construct(
        public ?int $id,
        public ?int $taxTypeId,
        public ?int $taxPayerId,
        public string $taxAmount,
        public string $lastPayment,
        public ?string $attachment,
        public TaxType $taxType,
        public TaxPayer $taxPayer,

    )
    {}
}
