<?php

namespace App\Application\TaxType\UseCases;

use App\Application\TaxType\DTOs\TaxTypeDTOs;
use App\Domain\TaxType\Repositories\TaxTypeRepositoryInterface;
use App\Domain\TaxType\Entities\TaxType;
use DomainException;

class CreateTaxTypeUseCase
{
    public function __construct(
        private TaxTypeRepositoryInterface $tax_type_repository
    )
    {}

    public function execute(TaxTypeDTOs $taxTypeDTOs)
    {
        $name = trim($taxTypeDTOs->name);

        if ($this->tax_type_repository->existsByName($name)) {
            throw new DomainException("نوع الضريبة مع الإسم [{$name}] موجود بالفعل.");
        }

        return $this->tax_type_repository->create(
            new TaxType(null , $name)
        );
    }
}
