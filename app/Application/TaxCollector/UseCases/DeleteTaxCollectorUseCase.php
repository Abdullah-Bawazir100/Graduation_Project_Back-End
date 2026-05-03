<?php

namespace App\Application\TaxCollector\UseCases;

use App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface;
use DomainException;

class DeleteTaxCollectorUseCase
{
    public function __construct(
        private TaxCollectorRepositoryInterface $taxCollectorRepository
    ) {}

    public function execute(int $id): void
    {
        $taxCollector = $this->taxCollectorRepository->findById($id);

        if (!$taxCollector) {
            throw new DomainException('المأمور مع ال ID [' . $id . '] غير موجود.');
        }

        $this->taxCollectorRepository->delete($id);
    }
}
