<?php

namespace App\Domain\Attachment\Entities;

use App\Domain\File\Entities\File;

class Attachment
{
    public function __construct(
        public ?int $id,
        public string $title,
        public string $attachmentFile,
        public File $file,
    ) {}
}
