<?php

namespace App\Application\TaxInformation\UseCases;

use App\Domain\TaxInformation\Repositories\TaxInformationRepositoryInterface;

class ListTaxInformationsUseCase
{
    public function __construct(
        private TaxInformationRepositoryInterface $tax_information_repository
    )
    {
    }

    public function execute()
    {
        return $this->tax_information_repository->getAll();
    }
}
