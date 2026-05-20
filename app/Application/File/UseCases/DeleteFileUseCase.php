<?php

namespace  App\Application\File\UseCases;

use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use DomainException;

class DeleteFileUseCase
{
    public function __construct(
        private FileRepositoryInterface $file_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository
    )
    {}

    public function execute(int $id): void
    {
        $file = $this->file_repository->findById($id);
        if (!$file) {
            throw new DomainException("الملف مع ال ID [$id] غير موجود.");
        }

        $this->file_repository->delete($id);
        //$this->tax_payer_repository->delete($file->taxPayer->id);
    }
}
