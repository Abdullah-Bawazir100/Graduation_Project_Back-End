<?php

namespace App\Application\FileMovement\DTOs;

class FileMovementDTOs{
    public function __construct(
        public ?string $status,
        public ?string $date,

        public ?int $fileId,
        public ?int $taxCollectorId,
        public ?int $departmentId,
    )
    {}
}
