<?php

namespace App\Application\CharitableCompany\UseCases;

use App\Domain\CharitableCompany\Repositories\CharitableCompanyRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class DeleteCharitableCompanyUseCase
{
    public function __construct(
        private CharitableCompanyRepositoryInterface $charitable_company_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(int $id): void
    {
        $charitableCompany = $this->charitable_company_repository->findById($id);
        if(!$charitableCompany)
        {
            throw new DomainException("لا يوجد ملف شركة خيرية مع ال ID [{$id}].");
        }
        $taxPayer = $this->tax_payer_repository->findById($charitableCompany->tax_payer_id);

        $this->charitable_company_repository->delete($id);
        $this->tax_payer_repository->delete($taxPayer->id);
    }
}
