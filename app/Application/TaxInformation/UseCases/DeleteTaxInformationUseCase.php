<?php

namespace App\Application\TaxInformation\UseCases;

use App\Domain\TaxInformation\Repositories\TaxInformationRepositoryInterface;
use DomainException;

class DeleteTaxInformationUseCase
{
    public function __construct(
        private TaxInformationRepositoryInterface $tax_information_repository,
    )
    {
    }

    public function execute(int $id): void
    {
        $taxInfo = $this->tax_information_repository->findById($id);
        if(!$taxInfo)
        {
            throw new DomainException("لا توجد معلومة ضريبية مع ال ID [$id].");
        }
        $this->tax_information_repository->delete($id);
    }
}
