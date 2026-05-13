<?php

namespace App\Application\TaxPayer\UseCases;

use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use DomainException;

class FindTaxPayerByIdUseCase
{
    public function __construct(
        private TaxPayerRepositoryInterface $tax_payer_repository
    )
    {}

    public function execute(int $id)
    {
        $taxPayer = $this->tax_payer_repository->findById($id);
        if(!$taxPayer)
        {
            return null;
        }
        return $taxPayer;
    }
}
