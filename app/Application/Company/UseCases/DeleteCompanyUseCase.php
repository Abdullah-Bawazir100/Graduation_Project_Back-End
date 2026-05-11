<?php

namespace App\Application\Company\UseCases;

use App\Domain\Company\Repositories\CompanyRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;

class DeleteCompanyUseCase
{
    public function __construct(
        private CompanyRepositoryInterface  $company_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository
    ) {}

    public function execute(int $id): void
    {
        $company = $this->company_repository->findById($id);
        if(!$company)
        {
            throw new \DomainException("لا يوجد ملف شركة مع ال ID [{$id}].");
        }
        $taxPayer = $this->tax_payer_repository->findById($company->tax_payer_id);
        $user = $this->user_repository->findById($taxPayer->userId);

        $this->company_repository->delete($id);
        $this->tax_payer_repository->delete($taxPayer->id);
        $this->user_repository->delete($user->id);
    }
}
