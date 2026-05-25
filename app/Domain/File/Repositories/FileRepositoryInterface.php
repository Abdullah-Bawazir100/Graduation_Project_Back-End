<?php

namespace App\Domain\File\Repositories;

use App\Domain\File\Entities\File;

interface FileRepositoryInterface
{
    public function create(File $file): File;
    public function update(File $file , int $id): ?File;
    public function getAll();
    public function findById(int $fileId): ?File;
    public function delete(int $fileId): void;
    public function countFiles(): int;
}
