<?php

namespace App\Domain\TaxPayer\Entities;

use App\Domain\TaxPayer\Enums\enFileType;

class TaxPayer
{
    public function __construct(
        public readonly ?int $id,
        public ?int $userId,
        public ?string $tradeName,
        public ?string $commercialRecord,
        public ?string $activityLicense,
        public ?string $tradePict,
        public ?string $insuranceCard,
        public ?string $propertyDocPict,
        public enFileType $fileType,
    ) {}
}
