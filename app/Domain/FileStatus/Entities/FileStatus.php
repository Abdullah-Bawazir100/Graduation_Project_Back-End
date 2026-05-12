<?php

namespace App\Domain\FileStatus\Entities;

class FileStatus
{
    public function __construct(
        public ?int $id,
        public string $statusName,
        public string $statusDescription,
    )
    {}
}
