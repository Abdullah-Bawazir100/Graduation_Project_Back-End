<?php

namespace App\Application\TaxPayer\UseCases;

use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;

class ListTaxPayersUseCase
{
    public function __construct(
        private TaxPayerRepositoryInterface $tax_payer_repository
    )
    {}

    public function execute()
    {
        return $this->tax_payer_repository->getAll();
    }
}
