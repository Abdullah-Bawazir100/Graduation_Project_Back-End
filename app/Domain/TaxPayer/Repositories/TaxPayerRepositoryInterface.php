<?php

namespace App\Domain\TaxPayer\Repositories;

use App\Domain\TaxPayer\Entities\TaxPayer;

interface TaxPayerRepositoryInterface
{
    public function create(TaxPayer $taxPayer): TaxPayer;
    public function update(TaxPayer $taxPayer , int $id): ?TaxPayer;
    public function delete(int $id): void;
    public function findById(int $id): ?TaxPayer;
    public function getAll();
    public function findByUserId(int $userId): ?TaxPayer;
    public function findByUserName(string $userName): ?TaxPayer;
    public function getTaxPayersWithSpecialInfo(?string $search = null);
    public function createFileToExistingTaxPayer(TaxPayer $taxPayer , int $userId): ?TaxPayer;
}
