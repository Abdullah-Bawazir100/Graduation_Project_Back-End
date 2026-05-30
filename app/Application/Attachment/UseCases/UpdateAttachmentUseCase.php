<?php

namespace App\Application\Attachment\UseCases;

use App\Application\Attachment\DTOs\AttachmentDTOs;
use App\Domain\Attachment\Entities\Attachment;
use App\Domain\Attachment\Repositories\AttachmentRepositoryInterface;
use App\Domain\File\Repositories\FileRepositoryInterface;
use DomainException;

class UpdateAttachmentUseCase
{
    public function __construct(
        private AttachmentRepositoryInterface $attachmentRepository,
        private FileRepositoryInterface $file_repository
    )
    {
    }

    public function execute(int $id, AttachmentDTOs $dto)
    {
        $attachment = $this->attachmentRepository->findById($id);
        $file = $this->file_repository->findById($dto->fileId);
        if(!$attachment)
        {
            throw new DomainException("لا يوجد مرفق ملف مع ال ID [$id].");
        }

        $updatedAttachment = new Attachment(
            id: $dto->id,
            title: $dto->title ?? $attachment->title,
            attachmentFile: $dto->attachmentFile ?? $attachment->attachmentFile,
            file: $file,
        );

        return $this->attachmentRepository->update($id, $updatedAttachment);
    }
}
