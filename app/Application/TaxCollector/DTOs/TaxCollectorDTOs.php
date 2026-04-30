<?php

namespace App\Application\TaxCollector\DTOs;

class TaxCollectorDTOs
{
    public function __construct(
        public ?int $id,
        public string $fullName,
        public string $idCard,
        public string $phone,
        public int $jobTypeId,
        public int $deptID,
    ) {}
}
