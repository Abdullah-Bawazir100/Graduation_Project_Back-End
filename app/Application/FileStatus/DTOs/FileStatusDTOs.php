<?php

namespace App\Application\FileStatus\DTOs;

class FileStatusDTOs
{
    public function __construct(
        public ?string $statusName,
        public ?string $statusDescription,
    )
    {}
}
