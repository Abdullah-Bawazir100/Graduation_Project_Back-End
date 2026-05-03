<?php

namespace App\Application\TaxCollector\UseCases;

use App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface;
use App\Domain\TaxCollector\Entities\TaxCollector;
use DomainException;

class FindByNameUseCase
{
    public function __construct(
        private TaxCollectorRepositoryInterface $taxCollectorRepository
    ) {}

    public function execute(string $name): ?TaxCollector
    {
        $taxCollector = $this->taxCollectorRepository->findByName($name);
        if(!$taxCollector)
        {
            throw new DomainException('المأمور مع الأسم [' . $name . '] غير موجود.');
        }
        return $taxCollector;
    }
}
