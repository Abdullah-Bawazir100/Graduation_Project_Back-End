<?php

namespace App\Application\TaxCollector\UseCases;

use App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface;

class ListTaxCollectorUseCase
{
    public function __construct(
        private TaxCollectorRepositoryInterface $taxCollectorRepository
    ) {}

    public function execute(): array
    {
        return $this->taxCollectorRepository->getAll();
    }
}
