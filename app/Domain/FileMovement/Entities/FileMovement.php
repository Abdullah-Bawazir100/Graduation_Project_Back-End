<?php

namespace App\Domain\FileMovement\Entities;

use App\Domain\Department\Entities\Department;
use App\Domain\File\Entities\File;
use App\Domain\FileMovement\Enums\enFileMovement;
use App\Domain\TaxCollector\Entities\TaxCollector;
use App\Domain\User\Entities\User;

class FileMovement
{
    public function __construct(
        public readonly ?int $id,
        public enFileMovement $status,
        public ?string $date,

        public File $file,
        public TaxCollector $taxCollector,
        public Department $department,
        public ?User $creator,
    )
    {}
}
