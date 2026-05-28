<?php

namespace App\Application\Company\UseCases;

use App\Application\Company\DTOs\CompanyDTOs;
use App\Application\TaxPayer\DTOs\TaxPayerDTOs;
use App\Domain\Company\Entities\Company;
use App\Domain\Company\Repositories\CompanyRepositoryInterface;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\TaxPayerMobile\Repositories\TaxPayerMobileRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class CreateCompanyFileToExistingTaxPayerUseCase
{
    public function __construct(
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private CompanyRepositoryInterface $company_repository,
        private UserRepositoryInterface $user_repository
    )
    {
    }

    public function execute(CompanyDTOs $companyDTOs , TaxPayerDTOs $taxPayerDTOs , int $userId, ?int $authenticatedUserId = null)
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

        // التحقق من صلاحيات القسم
        if ($authenticatedUserId !== null) {
            $actor = $this->user_repository->findById($authenticatedUserId);
            if ($actor && $actor->role !== UserRole::Admin) {
                if ((int)$actor->department->id !== (int)$existingUser->department->id) {
                    throw new DomainException('غير مصرح لك بإنشاء ملف شركة لمكلف من قسم غير القسم الذي تعمل فيه.');
                }
            }
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

        $company = new Company(
            id: null,
            tax_payer_id: $createdTaxPayer->id,
            articlesOfIncorporation: $companyDTOs->articlesOfIncorporation,
            govemorLicense: $companyDTOs->govemorLicense,
            partnersIDCards: $companyDTOs->partnersIDCards,
        );

        $createdCompany = $this->company_repository->create($company);

        return [
            'taxPayerInfo' => $createdTaxPayer,
            'companyInfo' => $createdCompany
        ];
    }
}
