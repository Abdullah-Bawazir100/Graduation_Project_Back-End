<?php

namespace App\Application\CharitableCompany\UseCases;

use App\Domain\CharitableCompany\Repositories\CharitableCompanyRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\File\Repositories\FileRepositoryInterface;

class ListCharitableCompaniesUseCase
{
    public function __construct(
        private CharitableCompanyRepositoryInterface $charitable_company_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository,
        private FileRepositoryInterface $file_repository
    )
    {}

    public function execute(int $authenticatedUserId)
    {
        $actor = $this->user_repository->findById($authenticatedUserId);
        $departmentId = ($actor && $actor->role !== UserRole::Admin) ? (int)$actor->department->id : null;
        $charitableCompanies = $this->charitable_company_repository->getAll($departmentId);

        $result = [];
        foreach ($charitableCompanies as $charitableCompany) {
            $taxPayer = null;
            $taxPayerUserInfo = null;

            if ($charitableCompany->taxPayerId) {
                $taxPayer = $this->tax_payer_repository->findById($charitableCompany->taxPayerId);

                if ($taxPayer && $taxPayer->fileId) {
                    $file = $this->file_repository->findById($taxPayer->fileId);
                    if ($file && $file->user) {
                        $taxPayerUserInfo = $file->user; // Return the full user object/array instead of selected fields
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
