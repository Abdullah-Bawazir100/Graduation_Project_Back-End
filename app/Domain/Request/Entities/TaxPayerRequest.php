<?php

namespace App\Domain\Request\Entities;

use App\Domain\Request\Enums\EnRequestStatus;
use App\Domain\TaxPayer\Enums\enFileType;

class TaxPayerRequest
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
        public ?string $articlesOfIncorporation,
        public ?string $govemorLicense,
        public ?string $partnersIDCards,
        public ?string $byLawsCopy,
        public EnRequestStatus $requestStatus,
        public ?string $note,
    )
    {}
}
