<?php

namespace App\Application\TaxCollector\UseCases;

use App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface;

class CountTaxCollectorsUseCase
{
    public function __construct(
        private TaxCollectorRepositoryInterface $repository
    ) {}

    public function execute(): int
    {
        return $this->repository->countTaxCollectors();
    }
}
