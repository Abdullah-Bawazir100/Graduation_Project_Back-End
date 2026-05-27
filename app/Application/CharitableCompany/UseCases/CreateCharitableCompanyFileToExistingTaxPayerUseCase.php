<?php

namespace App\Application\CharitableCompany\UseCases;

use App\Application\CharitableCompany\DTOs\CharitableCompanyDTOs;
use App\Application\TaxPayer\DTOs\TaxPayerDTOs;
use App\Domain\CharitableCompany\Entities\CharitableCompany;
use App\Domain\CharitableCompany\Repositories\CharitableCompanyRepositoryInterface;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class CreateCharitableCompanyFileToExistingTaxPayerUseCase
{
    public function __construct(
        private CharitableCompanyRepositoryInterface $charitable_company_repository,
        private UserRepositoryInterface $user_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository
    )
    {}

    public function execute(CharitableCompanyDTOs $charitableCompanyDTOs ,
    TaxPayerDTOs $taxPayerDTOs , int $userId)
    {
        $existingUser = $this->user_repository->findById($userId);
        if(!$existingUser)
        {
            throw new DomainException("المستخدم المكلف مع ال ID [$userId] غير موجود.");
        }
        if($existingUser->role !== UserRole::Tax_Payer)
        {
            throw new DomainException("المستخدم المكلف مع ال ID [$userId] ليس مكلف.");
        }

        $taxPayer = new TaxPayer(
            id: null,
            userId: $userId,
            tradeName: $taxPayerDTOs->tradeName,
            commercialRecord: $taxPayerDTOs->commercialRecord,
            activityLicense: $taxPayerDTOs->activityLicense,
            tradePict: $taxPayerDTOs->tradePict,
            insuranceCard: $taxPayerDTOs->insuranceCard,
            propertyDocPict: $taxPayerDTOs->propertyDocPict,
            fileType: $taxPayerDTOs->getFileType(),
            source: $taxPayerDTOs->source
        );

        $createdTaxPayer = $this->tax_payer_repository->create($taxPayer);

        $charitableCompany = new CharitableCompany(
            id: null,
            tax_payer_id: $createdTaxPayer->id,
            byLawsCopy:  $charitableCompanyDTOs->byLawsCopy,
        );

        $createdCharitableCompany = $this->charitable_company_repository->create($charitableCompany);
        return [
            'charitableCompanyInfo' => $createdCharitableCompany,
            'taxPayerInfo' => $createdTaxPayer,
        ];
    }
}
