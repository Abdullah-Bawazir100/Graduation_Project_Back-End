<?php

namespace App\Application\TaxType\UseCases;

use App\Application\TaxType\DTOs\TaxTypeDTOs;
use App\Domain\TaxType\Entities\TaxType;
use App\Domain\TaxType\Repositories\TaxTypeRepositoryInterface;
use DomainException;

class UpdateTaxTypeUseCase
{
    public function __construct(
        private TaxTypeRepositoryInterface $tax_type_repository
    ) {}

    public function execute(int $id, TaxTypeDTOs $jobTypeDTOs): TaxType
    {
        $taxType = $this->tax_type_repository->findById($id);

        if (!$taxType) {
            throw new DomainException("نوع الضريبة مع ال ID [{$id} غير موجود.");
        }

        $name = $jobTypeDTOs->name ?? $taxType->name;

        if (
            $name !== $taxType->name &&
            $this->tax_type_repository->existsByName($name)
        ) {
            throw new DomainException("نوع الضريبة مع الأسم [ $name}] موجود بالفعل.");
        }

        return $this->tax_type_repository->update(
            new TaxType($id, $name)
        );
    }
}
