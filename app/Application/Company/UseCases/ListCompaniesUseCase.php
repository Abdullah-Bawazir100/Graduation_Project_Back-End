<?php

namespace App\Application\Company\UseCases;

use App\Domain\Company\Repositories\CompanyRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\File\Repositories\FileRepositoryInterface;

class ListCompaniesUseCase
{
    public function __construct(
        private CompanyRepositoryInterface $company_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository,
        private FileRepositoryInterface $file_repository
    )
    {}

    public function execute(int $authenticatedUserId)
    {
        $actor = $this->user_repository->findById($authenticatedUserId);
        $departmentId = ($actor && $actor->role !== UserRole::Admin) ? (int)$actor->department->id : null;

        $companies = $this->company_repository->getAll($departmentId);

        $result = [];
        foreach ($companies as $company) {
            $taxPayer = null;
            $taxPayerUserInfo = null;

            if ($company->tax_payer_id) {
                $taxPayer = $this->tax_payer_repository->findById($company->tax_payer_id);

                if ($taxPayer && $taxPayer->fileId) {
                    $file = $this->file_repository->findById($taxPayer->fileId);
                    if ($file && $file->user) {
                        $taxPayerUserInfo = $file->user;
                    }
                }
            }

            $result[] = [
                'companyInfo' => $company,
                'taxPayerInfo' => $taxPayer,
                'userInfo' => $taxPayerUserInfo->toArray(),
            ];
        }

        return $result;
    }
}
