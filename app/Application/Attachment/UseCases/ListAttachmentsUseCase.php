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
        $user = \Illuminate\Support\Facades\Auth::user();
        $attachments = $this->attachment_repository->getAll();

        $attachmentsCollection = collect($attachments);

        if ($user && $user->role !== \App\Domain\User\Enums\UserRole::Admin) {
            $attachmentsCollection = $attachmentsCollection->filter(function ($attachment) use ($user) {
                return $attachment->file->department->id === $user->department_id;
            })->values();
        }

        return [
            'attachments' => $attachmentsCollection->all(),
            'total_count' => $attachmentsCollection->count(),
        ];
    }
}
