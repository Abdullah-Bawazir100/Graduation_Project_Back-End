<?php

namespace App\Application\TaxCollector\UseCases;

use App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface;
use App\Domain\TaxCollector\Entities\TaxCollector;

class FindByNameUseCase
{
    public function __construct(
        private TaxCollectorRepositoryInterface $taxCollectorRepository
    ) {}

    public function execute(string $name): ?TaxCollector
    {
        return $this->taxCollectorRepository->findByName($name);
    }
}