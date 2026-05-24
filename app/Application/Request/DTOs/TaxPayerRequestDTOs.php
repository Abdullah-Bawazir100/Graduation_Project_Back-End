<?php

namespace App\Application\Request\DTOs;

use App\Domain\Request\Enums\EnRequestStatus;
use App\Domain\TaxPayer\Enums\enFileType;

class TaxPayerRequestDTOs
{
    public function __construct(
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

        public enRequestStatus $requestStatus,
        public ?string $note,
        public ?string $source
    )
    {}

    public function getRequestStatus(): enRequestStatus
    {
        if ($this->requestStatus instanceof EnRequestStatus) {
            return $this->requestStatus;
        }

        if ($this->requestStatus !== null) {
            return enRequestStatus::from($this->requestStatus);
        }

        return enRequestStatus::Pending;
    }
}
