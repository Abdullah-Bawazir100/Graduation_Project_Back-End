<?php

namespace App\Domain\TaxInformation\Entities;

use App\Domain\File\Entities\File;
use App\Domain\TaxType\Entities\TaxType;

class TaxInformation
{
    public function __construct(
        public ?int $id,
        public ?int $taxTypeId,
        public ?int $fileId,
        public string $taxAmount,
        public string $lastPayment,
        public ?string $attachment,
        public TaxType $taxType,
        public ?File $file,
    )
    {}
}
