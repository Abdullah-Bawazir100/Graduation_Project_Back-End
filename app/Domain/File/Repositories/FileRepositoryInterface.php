<?php

namespace App\Domain\File\Repositories;

use App\Domain\File\Entities\File;
use App\Domain\TaxPayer\Enums\enFileType;

interface FileRepositoryInterface
{
    public function create(File $file): File;
    public function update(File $file , int $id): ?File;
    public function getAll(?string $search = null, ?int $departmentId = null);
    public function findById(int $fileId): ?File;
    public function getFileByTaxPayerId(int $taxPayerId , enFileType $fileType);
    public function delete(int $fileId): void;
    public function countFiles(): int;
    public function countFilesByType(enFileType $type): int;
}
