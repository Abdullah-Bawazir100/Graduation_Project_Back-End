<?php

namespace App\Domain\TaxPayer\Entities;

use App\Domain\District\Entities\District;
use App\Domain\Region\Entities\Region;
use App\Domain\TaxPayer\Enums\enFileType;

class TaxPayer implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public ?int $fileId,
        public ?string $tradeName,
        public ?string $commercialRecord,
        public ?string $activityLicense,
        public ?string $tradePict,
        public ?string $insuranceCard,
        public ?string $propertyDocPict,
        public enFileType $fileType,
        public ?string $source,
        public ?Region $region = null,
        public ?District $district = null,
        public ?array $companies = null,
        public ?array $charitableCompanies = null,
    ) {}

    public function jsonSerialize(): mixed
    {
        $data = [
            'id' => $this->id,
            'fileId' => $this->fileId,
            'tradeName' => $this->tradeName,
            'commercialRecord' => $this->commercialRecord,
            'activityLicense' => $this->activityLicense,
            'tradePict' => $this->tradePict,
            'insuranceCard' => $this->insuranceCard,
            'propertyDocPict' => $this->propertyDocPict,
            'fileType' => $this->fileType,
            'source' => $this->source,
            'region' => $this->region,
            'district' => $this->district,
        ];

        if ($this->fileType === enFileType::Company) {
            $data['companyInfo'] = $this->companies && count($this->companies) > 0 ? $this->companies[0] : null;
        } elseif ($this->fileType === enFileType::CharitableCompany) {
            $data['charitableCompanyInfo'] = $this->charitableCompanies && count($this->charitableCompanies) > 0 ? $this->charitableCompanies[0] : null;
        }

        return $data;
    }
}
