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
        public User $user,
        public Department $department,
        public FileStatus $fileStatus,
        public Activity_Type $activityType,
        public PaymentType $paymentType,
        public ?User $creator,

        public array $taxPayers = []
    ) {}
}
