<?php

namespace  App\Application\Attachment\UseCases;

use App\Domain\Attachment\Repositories\AttachmentRepositoryInterface;
use DomainException;

class DeleteAttachmentUseCase
{
    public function __construct(
        private AttachmentRepositoryInterface $attachmentRepository,
    )
    {
    }

    public function execute(int $id)
    {
        $attachment = $this->attachmentRepository->findById($id);
        if (!$attachment) {
            throw new DomainException("لا يوجد مرفق ملف مع ال ID [$id].");
        }
        return $this->attachmentRepository->delete($id);
    }
}

