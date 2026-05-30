<?php

namespace App\Application\Attachment\UseCases;

use App\Application\Attachment\DTOs\AttachmentDTOs;
use App\Domain\Attachment\Entities\Attachment;
use App\Domain\Attachment\Repositories\AttachmentRepositoryInterface;
use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class CreateAttachmentUseCase
{
    public function __construct(
        private AttachmentRepositoryInterface $attachment_repository,
        private FileRepositoryInterface $file_repository,
        private UserRepositoryInterface $user_repository
    )
    {
    }

    public function execute(AttachmentDTOs $fileDTOs , ?int $authenticatedUserId): Attachment
    {
        //$actor = $this->user_repository->findById($authenticatedUserId);

        $file = $this->file_repository->findById($fileDTOs->fileId);
        if(!$file)
        {
            throw new DomainException("لا يوجد ملف مع ال ID [$fileDTOs->fileId].");
        }

        // if($actor->role !== UserRole::Admin)
        // {
        //     if()
        //     {

        //     }
        // }

        $attachment = new Attachment(
            id: null,
            title: $fileDTOs->title,
            attachmentFile: $fileDTOs->attachmentFile,
            file: $file,
        );

        $createdAttachment = $this->attachment_repository->create($attachment);
        return $createdAttachment;
    }
}
