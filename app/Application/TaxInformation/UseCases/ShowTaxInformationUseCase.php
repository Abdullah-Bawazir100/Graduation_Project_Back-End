<?php

namespace App\Application\TaxInformation\UseCases;

use App\Domain\TaxInformation\Repositories\TaxInformationRepositoryInterface;
use DomainException;

class ShowTaxInformationUseCase
{
    public function __construct(
        private TaxInformationRepositoryInterface $tax_information_repository
    ) {
    }

    public function execute(int $id): ?object
    {
        $taxInfo = $this->tax_information_repository->findById($id);
        if(!$taxInfo)
        {
            throw new DomainException("لا توجد معلومات ضريبة مع ال ID [$id].");
        }
        return $taxInfo;
    }
}
