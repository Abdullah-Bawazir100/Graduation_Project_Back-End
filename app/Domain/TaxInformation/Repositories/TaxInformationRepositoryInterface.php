<?php

namespace  App\Domain\TaxInformation\Repositories;

use App\Domain\TaxInformation\Entities\TaxInformation;

interface TaxInformationRepositoryInterface
{
    public function create(TaxInformation $taxInformation): TaxInformation;
    public function update(TaxInformation $taxInformation): ?TaxInformation;
    public function findById(int $id);
    public function getAll();
    public function delete(int $id): void;

    public function moveTaxInformationToAnotherTaxType(int $oldTaxTypeId , int $newTaxTypeId);
}
