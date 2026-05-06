<?php

namespace App\Application\TaxType\UseCases;

use App\Domain\JobType\Repositories\JobTypeRepositoryInterface;
use App\Domain\TaxType\Repositories\TaxTypeRepositoryInterface;
use DomainException;

class DeleteTaxTypeUseCase
{
    public function __construct(private TaxTypeRepositoryInterface $tax_type_repository)
    {
    }

    public function execute(int $id): void
    {
        $jobType = $this->tax_type_repository->findById($id);

        if (!$jobType) {
            throw new DomainException("نوع الضريبة مع ال ID [{$id}] غير موجود.");
        }

        $this->tax_type_repository->delete($id);
    }
}

