<?php

namespace App\Application\TaxPayer\DTOs;

use App\Domain\TaxPayer\Enums\enFileType;
use App\Domain\User\Entities\User;

class TaxPayerDTOs
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
        public ?string $source
    )
    {}

    public function getFileType(): enFileType
    {
        if ($this->fileType instanceof enFileType) {
            return $this->fileType;
        }

        if ($this->fileType !== null) {
            return enFileType::from($this->fileType);
        }

        return enFileType::Individual;
    }
}
