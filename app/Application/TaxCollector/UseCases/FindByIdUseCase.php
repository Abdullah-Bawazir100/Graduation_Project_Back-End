<?php

namespace App\Application\TaxCollector\UseCases;

use App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface;
use App\Domain\TaxCollector\Entities\TaxCollector;
use DomainException;

class FindByIdUseCase
{
    public function __construct(
        private TaxCollectorRepositoryInterface $taxCollectorRepository
    ) {}

    public function execute(int $id): ?TaxCollector
    {
        $taxCollector =  $this->taxCollectorRepository->findById($id);
        if(!$taxCollector)
        {
            throw new DomainException('المأمور مع ال ID [' . $id . '] غير موجود.');
        }
        return $taxCollector;
    }
}
