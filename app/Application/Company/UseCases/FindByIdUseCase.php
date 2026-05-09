<?php

namespace App\Application\Company\UseCases;

use App\Domain\Company\Repositories\CompanyRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;

class FindByIdUseCase
{
    public function __construct(
        private CompanyRepositoryInterface $company_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(int $id)
    {
        $company = $this->company_repository->findById($id);
        if(!$company)
        {
            throw new \DomainException("ملف الشركة مع ال ID [{$id}] غير موجود.");
        }

        $taxPayer = null;
        $taxPayerUserInfo = null;

        if ($company->tax_payer_id) {
            $taxPayer = $this->tax_payer_repository->findById($company->tax_payer_id);

            if ($taxPayer && $taxPayer->userId) {
                $user = $this->user_repository->findById($taxPayer->userId);
                if ($user) {
                    $taxPayerUserInfo = $user; // Return the full user object/array instead of selected fields
                }
            }
        }

        return [
            'companyInfo' => $company,
            'taxPayerInfo' => $taxPayer,
            'userInfo' => $taxPayerUserInfo->toArray()
        ];
    }
}
