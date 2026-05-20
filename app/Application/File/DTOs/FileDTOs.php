<?php

namespace App\Application\File\DTOs;

class FileDTOs
{
    public function __construct(
        public ?string $taxNumber,
        public ?string $inventoryNumber,
        public ?string $activityStartDate,
        public ?int $docsCount,
        public ?string $note,
        public ?int $taxPayerId,
        public ?int $departmentId,
        public ?int $fileStatusId,
        public ?int $activityTypeId,
        public ?int $paymentTypeId,
        public ?int $regionId,
        public ?int $districtId,
    ) {}

}
