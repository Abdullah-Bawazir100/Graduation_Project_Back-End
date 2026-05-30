<?php

namespace App\Application\Attachment\UseCases;

use App\Domain\Attachment\Repositories\AttachmentRepositoryInterface;

class ListAttachmentsUseCase
{

    public function __construct(
        private AttachmentRepositoryInterface $attachment_repository
    )
    {
    }

    public function execute()
    {
        $attachments = $this->attachment_repository->getAll();
        return $attachments;
    }
}
