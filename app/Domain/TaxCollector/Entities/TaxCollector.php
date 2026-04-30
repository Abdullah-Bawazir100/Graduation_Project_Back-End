<?php

namespace App\Domain\TaxCollector\Entities;

use App\Domain\JobType\Entities\JobType;
use App\Domain\Department\Entities\Department;

class TaxCollector
{
    public function __construct(
        public ?int $id,
        public string $fullName,
        public string $idCard,
        public string $phone,
        public int $jobTypeId,
        public int $deptID,
        public ?JobType $jobType = null,
        public ?Department $department = null,
    ) {}
}
