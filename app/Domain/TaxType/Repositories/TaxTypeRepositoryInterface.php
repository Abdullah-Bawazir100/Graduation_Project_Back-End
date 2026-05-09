<?php

namespace  App\Domain\TaxType\Repositories;

use App\Domain\TaxType\Entities\TaxType;

interface TaxTypeRepositoryInterface
{
    public function create(TaxType $taxType): TaxType;
    public function update(TaxType $taxType): ?TaxType;
    public function findById(int $id): ?TaxType;
    public function getAll();
    public function existsByName(string $name): bool;
    public function delete(int $id): void;
    //public function moveTaxInformationToAnotherTaxType(int $oldTaxTypeId , int $newTaxTypeId);

}
