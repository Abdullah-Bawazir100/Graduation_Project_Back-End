<?php

namespace  App\Domain\TaxPayer\UseCases;

use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;

class UpdateTaxPayerUseCase
{
    public function __construct(
        private TaxPayerRepositoryInterface $tax_payer_repository
    )
    {}

    public function execute()
    {
        
    }
}
