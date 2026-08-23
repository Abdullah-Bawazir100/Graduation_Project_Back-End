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
use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\Region\Repositories\RegionRepositoryInterface;
use App\Domain\District\Repositories\DistrictRepositoryInterface;
use DomainException;

class CreateCharitableCompanyFileToExistingTaxPayerUseCase
{
    public function __construct(
        private CharitableCompanyRepositoryInterface $charitable_company_repository,
        private UserRepositoryInterface $user_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private FileRepositoryInterface $file_repository,
        private RegionRepositoryInterface $region_repository,
        private DistrictRepositoryInterface $district_repository
    )
    {}

    public function execute(CharitableCompanyDTOs $charitableCompanyDTOs ,
    TaxPayerDTOs $taxPayerDTOs , int $fileId , ?int $authenticatedUserId = null)
    {
        $file = $this->file_repository->findById($fileId);
        if(!$file)
        {
            throw new DomainException("الملف مع ال ID [$fileId] غير موجود.");
        }
        $existingUser = $file->user;
        if($existingUser->role !== UserRole::Tax_Payer)
        {
            throw new DomainException("المستخدم المرتبط بالملف ليس مكلف.");
        }

        if ($authenticatedUserId !== null) {
            $actor = $this->user_repository->findById($authenticatedUserId);
            if ($actor && $actor->role !== UserRole::Admin) {
                if ((int)$actor->department->id !== (int)$existingUser->department->id) {
                    throw new DomainException('غير مصرح لك بإنشاء ملف شركة خيرية لمكلف من قسم غير القسم الذي تعمل فيه.');
                }
            }
        }

        $region = null;
        $district = null;


        if ($taxPayerDTOs->regionId && $taxPayerDTOs->districtId) {
            $region = $this->region_repository->findById($taxPayerDTOs->regionId);
            $district = $this->district_repository->findById($taxPayerDTOs->districtId);

            if ($district && $region && $district->region->id != $region->id) {
                throw new DomainException("الحي المحدد غير مربوط بالمنطقة المحددة.");
            }
        }

        $taxPayer = new TaxPayer(
            id: null,
            fileId: $fileId,
            tradeName: $taxPayerDTOs->tradeName,
            commercialRecord: $taxPayerDTOs->commercialRecord,
            activityLicense: $taxPayerDTOs->activityLicense,
            tradePict: $taxPayerDTOs->tradePict,
            insuranceCard: $taxPayerDTOs->insuranceCard,
            propertyDocPict: $taxPayerDTOs->propertyDocPict,
            fileType: $taxPayerDTOs->getFileType(),
            source: $taxPayerDTOs->source,
            region: $region,
            district: $district
        );

        $createdTaxPayer = $this->tax_payer_repository->create($taxPayer);

        $charitableCompany = new CharitableCompany(
            id: null,
            taxPayerId: $createdTaxPayer->id,
            byLawsCopy:  $charitableCompanyDTOs->byLawsCopy,
        );

        $createdCharitableCompany = $this->charitable_company_repository->create($charitableCompany);
        return [
            'fileInfo' => $file,
            'taxPayerInfo' => $createdTaxPayer,
            'charitableCompanyInfo' => $createdCharitableCompany,
        ];
    }
}
