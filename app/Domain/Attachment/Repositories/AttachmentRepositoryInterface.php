<?php

namespace App\Domain\Attachment\Repositories;
use App\Domain\Attachment\Entities\Attachment;

interface AttachmentRepositoryInterface
{
    public function create(Attachment $attachment): Attachment;
    public function update(int $id , Attachment $attachment): ?Attachment;
    public function getAll();
    public function findById(int $id): ?Attachment;
    public function delete(int $id);
}
