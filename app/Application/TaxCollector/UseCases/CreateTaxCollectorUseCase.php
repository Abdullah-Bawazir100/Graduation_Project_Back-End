<?php

namespace App\Application\TaxCollector\UseCases;

use App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface;
use App\Application\TaxCollector\DTOs\TaxCollectorDTOs;
use App\Domain\TaxCollector\Entities\TaxCollector;

class CreateTaxCollectorUseCase
{
    public function __construct(
        private TaxCollectorRepositoryInterface $taxCollectorRepository
    ) {}

    public function execute(TaxCollectorDTOs $dto): TaxCollector
    {
        $taxCollector = new TaxCollector(
            id: null,
            fullName: $dto->fullName,
            idCard: $dto->idCard,
            phone: $dto->phone,
            jobTypeId: $dto->jobTypeId,
            deptID: $dto->deptID
        );

        return $this->taxCollectorRepository->create($taxCollector);
    }
}
