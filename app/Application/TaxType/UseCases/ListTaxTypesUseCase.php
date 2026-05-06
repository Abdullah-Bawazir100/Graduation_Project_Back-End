<?php

namespace App\Application\TaxTYpe\UseCases;

use App\Domain\TaxType\Repositories\TaxTypeRepositoryInterface;

class ListTaxTypesUseCase
{
    public function __construct(
        private TaxTypeRepositoryInterface $tax_type_repository
    ) {}

    public function execute()
    {
        return $this->tax_type_repository->getAll();
    }
}
