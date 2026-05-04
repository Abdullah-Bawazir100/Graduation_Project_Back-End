<?php

namespace App\Application\Company\UseCases;

use App\Application\Company\DTOs\CompanyDTOs;
use App\Domain\Company\Entities\Company;
use App\Domain\Company\Repositories\CompanyRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class UpdateCompanyUseCase
{
    public function __construct(
        private CompanyRepositoryInterface $company_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(CompanyDTOs $companyDTOs , int $id)
    {
        $existingCompany = $this->company_repository->findById($id);
        $existingTaxPayer = $this->tax_payer_repository->findById($existingCompany->tax_payer_id);
        $existingUser = $this->user_repository->findById($existingTaxPayer->userId);
        if(!$existingCompany)
        {
            throw new DomainException("ملف الشركة مع ال ID [{$id}] غير موجود.");
        }
        $company = new Company(
            id: $id,
            tax_payer_id: $existingTaxPayer->id,
            articlesOfIncorporation: $companyDTOs->articlesOfIncorporation,
            govemorLicense: $companyDTOs->govemorLicense,
            partnersIDCards: $companyDTOs->partnersIDCards,
        );
        $updatedCompany = $this->company_repository->update($company , $id);
        return [
            'UpdatedCompany' => $updatedCompany,
            'taxPayerInfo' => $existingTaxPayer,
            'userInfo' => $existingUser
        ];

    }
}
