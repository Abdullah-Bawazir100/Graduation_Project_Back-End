<?php

namespace App\Application\Company\UseCases;

use App\Domain\Company\Repositories\CompanyRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;

class ListCompaniesUseCase
{
    public function __construct(
        private CompanyRepositoryInterface $company_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute()
    {
        $companies = $this->company_repository->getAll();

        $result = [];
        foreach ($companies as $company) {
            $taxPayer = null;
            $taxPayerUserInfo = null;

            if ($company->tax_payer_id) {
                $taxPayer = $this->tax_payer_repository->findById($company->tax_payer_id);

                if ($taxPayer && $taxPayer->userId) {
                    $user = $this->user_repository->findById($taxPayer->userId);
                    if ($user) {
                        $taxPayerUserInfo = [
                            'id' => $user->id,
                            'fullName' => $user->firstName . ' ' . $user->lastName,
                            'userName' => $user->userName,
                            'phone' => $user->phone,
                            'fileType' => $taxPayer->fileType,
                        ];
                    }
                }
            }

            $result[] = [
                'company' => $company,
                'taxPayerInfo' => $taxPayerUserInfo
            ];
        }

        return $result;
    }
}
