<?php

namespace App\Application\TaxCollector\UseCases;

use App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface;
use App\Domain\TaxCollector\Entities\TaxCollector;

class FindByIdUseCase
{
    public function __construct(
        private TaxCollectorRepositoryInterface $taxCollectorRepository
    ) {}

    public function execute(int $id): ?TaxCollector
    {
        return $this->taxCollectorRepository->findById($id);
    }
}