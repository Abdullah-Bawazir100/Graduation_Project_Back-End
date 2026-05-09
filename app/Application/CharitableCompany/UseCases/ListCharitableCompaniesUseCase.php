<?php

namespace App\Application\CharitableCompany\UseCases;

use App\Domain\CharitableCompany\Repositories\CharitableCompanyRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;

class ListCharitableCompaniesUseCase
{
    public function __construct(
        private CharitableCompanyRepositoryInterface $charitable_company_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute()
    {
        $charitableCompanies = $this->charitable_company_repository->getAll();

        $result = [];
        foreach ($charitableCompanies as $charitableCompany) {
            $taxPayer = null;
            $taxPayerUserInfo = null;

            if ($charitableCompany->tax_payer_id) {
                $taxPayer = $this->tax_payer_repository->findById($charitableCompany->tax_payer_id);

                if ($taxPayer && $taxPayer->userId) {
                    $user = $this->user_repository->findById($taxPayer->userId);
                    if ($user) {
                        $taxPayerUserInfo = $user; // Return the full user object/array instead of selected fields
                    }
                }
            }

            $result[] = [
                'charitableCompanyInfo' => $charitableCompany,
                'taxPayerInfo' => $taxPayer,
                'userInfo' => $taxPayerUserInfo->toArray()
            ];
        }

        return $result;
    }
}
