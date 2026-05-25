<?php

namespace App\Application\FileMovement\DTOs;

use App\Domain\FileMovement\Enums\enFileMovement;
use Illuminate\Support\Carbon;

class FileMovementDTOs{
    public function __construct(
        public enFileMovement $status,
        public ?string $date,

        public ?int $fileId,
        public ?int $taxCollectorId,
        public ?int $departmentId,
    )
    {}

    public function getDateAsCarbon(): ?Carbon
    {
        return $this->date ? Carbon::parse($this->date) : null;
    }
}
