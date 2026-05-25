<?php

namespace App\Domain\FileMovement\Entities;

use App\Domain\Department\Entities\Department;
use App\Domain\File\Entities\File;
use App\Domain\FileMovement\Enums\enFileMovement;
use App\Domain\TaxCollector\Entities\TaxCollector;
use App\Domain\User\Entities\User;
use Illuminate\Support\Carbon;

class FileMovement implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public enFileMovement $status,
        public ?Carbon $date,

        public File $file,
        public TaxCollector $taxCollector,
        public Department $department,
        public ?User $creator
    )
    {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'date' => $this->date?->format('Y-m-d'),
            'file' => $this->file,
            'taxCollector' => $this->taxCollector,
            'department' => $this->department,
            'creator' => $this->creator,
        ];
    }
}
