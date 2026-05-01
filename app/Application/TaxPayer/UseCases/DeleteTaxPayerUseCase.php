<?php

namespace App\Application\TaxPayer\UseCases;

use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use DomainException;

class DeleteTaxPayerUseCase
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
            throw new DomainException("دافع الضرائب مع ال ID [{$id}] غير موجود.");
        }
        $this->tax_payer_repository->delete($id);
    }
}
