<?php

namespace App\Domain\File\Entities;

use App\Domain\Activity_Type\Entities\Activity_Type;
use App\Domain\ActivityType\Entities\ActivityType;
use App\Domain\Department\Entities\Department;
use App\Domain\District\Entities\District;
use App\Domain\FileStatus\Entities\FileStatus;
use App\Domain\PaymentType\Entities\PaymentType;
use App\Domain\Region\Entities\Region;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\User\Entities\User;

class File
{
    public function __construct(

        public readonly ?int $id,
        public ?string $taxNumber,
        public string $inventoryNumber,
        public ?string $activityStartDate,
        public int $docsCount,
        public ?string $note,
        public ?string $fullAddress,
        public TaxPayer $taxPayer,
        public Department $department,
        public FileStatus $fileStatus,
        public Activity_Type $activityType,
        public PaymentType $paymentType,
        public Region $region,
        public District $district,
        public ?User $creator,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'taxNumber' => $this->taxNumber,
            'inventoryNumber' => $this->inventoryNumber,
            'activityStartDate' => $this->activityStartDate,
            'docsCount' => $this->docsCount,
            'note' => $this->note,
            'fullAddress' => $this->fullAddress,
            'taxPayer' => $this->taxPayer,
            'department' => $this->department,
            'fileStatus' => $this->fileStatus,
            'activityType' => $this->activityType,
            'paymentType' => $this->paymentType,
            'region' => $this->region,
            'district' => $this->district,
            'createdBy' => $this->creator?->id,
            'creator' => $this->creator ? $this->creator->toArray() : null,
        ];
    }
}
