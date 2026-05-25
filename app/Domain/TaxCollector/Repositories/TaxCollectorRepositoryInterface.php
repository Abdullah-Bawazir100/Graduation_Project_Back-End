<?php

namespace App\Domain\TaxCollector\Repositories;

use App\Domain\TaxCollector\Entities\TaxCollector;

interface TaxCollectorRepositoryInterface
{
    public function create(TaxCollector $taxCollector): TaxCollector;
    public function update(TaxCollector $taxCollector): TaxCollector;
    public function delete(int $id): void;
    public function findById(int $id): ?TaxCollector;
    public function getAll();
    public function findByName(string $name): ?TaxCollector;
    public function countTaxCollectors(): int;
}
