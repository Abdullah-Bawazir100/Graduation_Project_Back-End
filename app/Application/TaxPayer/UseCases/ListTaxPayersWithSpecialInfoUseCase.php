<?php

namespace App\Application\TaxPayer\UseCases;

use App\Domain\CharitableCompany\Repositories\CharitableCompanyRepositoryInterface;
use App\Domain\Company\Repositories\CompanyRepositoryInterface;
use App\Domain\TaxPayer\Enums\enFileType;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\File\Repositories\FileRepositoryInterface;

class ListTaxPayersWithSpecialInfoUseCase
{
    public function __construct(
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository,
        private CompanyRepositoryInterface $company_repository,
        private CharitableCompanyRepositoryInterface $charitable_company_repository,
        private FileRepositoryInterface $file_repository
    )
    {}

    public function execute(?string $search = null, ?int $authenticatedUserId = null)
    {
        $departmentId = null;
        if ($authenticatedUserId !== null) {
            $actor = $this->user_repository->findById($authenticatedUserId);
            if ($actor && $actor->role !== UserRole::Admin) {
                $departmentId = (int)$actor->department->id;
            }
        }

        $taxPayersInfo = $this->tax_payer_repository->getTaxPayersWithSpecialInfo($search, $departmentId);

        $result = [];

        foreach ($taxPayersInfo as $taxPayer) {

            $userInfo = null;
            $companyId = null;
            $charitableCompanyId = null;

            if ($taxPayer->fileId) {
                $file = $this->file_repository->findById($taxPayer->fileId);

                if ($file && $file->user) {
                    $userInfo = $file->user;
                }
            }

            // إذا الملف ليس Individual
            if ($taxPayer->fileType !== enFileType::Individual) {

                $companyInfo = $this->company_repository
                    ->findByTaxPayerId($taxPayer->id);

                $charitableCompanyInfo = $this->charitable_company_repository
                    ->findByTaxPayerId($taxPayer->id);

                $companyId = $companyInfo?->id;
                $charitableCompanyId = $charitableCompanyInfo?->id;
            }

            $result[] = [
                'fileId' => $taxPayer->fileId,
                'taxPayerId' => $taxPayer->id,
                'taxPayerName' => $userInfo
                    ? $userInfo->firstName . ' ' . $userInfo->lastName : null,
                'tradeName' => $taxPayer->tradeName,
                'phone' => $userInfo?->phone,
                'taxPayerFileType' => $taxPayer->fileType,
                'companyId' => $companyId,
                'charitableCompanyId' => $charitableCompanyId,
            ];
        }

        return $result;
    }
}
