<?php

namespace App\Application\TaxType\UseCases;

use App\Domain\JobType\Entities\JobType;
use App\Domain\TaxType\Entities\TaxType;
use App\Domain\TaxType\Repositories\TaxTypeRepositoryInterface;
use DomainException;

class ShowTaxTypeUseCase
{
    public function __construct(private TaxTypeRepositoryInterface $tax_type_repository) {}

    public function execute(int $id): ?TaxType
    {
        $taxType = $this->tax_type_repository->findById($id);

        if (!$taxType) {
            throw new DomainException("نوع الضريبة مع ال ID [{$id}] غير موجودة.");
        }

        return $taxType;
    }
}
