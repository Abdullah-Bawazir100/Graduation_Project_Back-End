<?php

namespace App\Application\Attachment\DTOs;

class AttachmentDTOs
{
    public function __construct(
        public readonly ?int $id,
        public ?string $title,
        public ?string $attachmentFile,
        public ?int $fileId,
    ) {
    }
}
