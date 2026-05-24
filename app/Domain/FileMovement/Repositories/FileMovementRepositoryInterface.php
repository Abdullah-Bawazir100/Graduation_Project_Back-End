<?php

namespace App\Domain\FileMovement\Repositories;

use App\Domain\FileMovement\Entities\FileMovement;

interface FileMovementRepositoryInterface
{
    public function create(FileMovement $fileMovement): FileMovement;
    public function update(FileMovement $fileMovement , int $id): ?FileMovement;
    public function findById(int $fileMovementId): ?FileMovement;
    public function findFileMovementByFileId(int $fileId): ?FileMovement;
    public function getAll();
    public function delete(int $id): void;
}
