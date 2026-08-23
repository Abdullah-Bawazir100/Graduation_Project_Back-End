<?php

namespace App\Application\TaxPayer\UseCases;

use App\Application\TaxPayer\DTOs\TaxPayerDTOs;
use App\Domain\District\Repositories\DistrictRepositoryInterface;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\Region\Repositories\RegionRepositoryInterface;
use DomainException;

class CreateFileToExistingTaxPayerUseCase
{
    public function __construct(
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository,
        private FileRepositoryInterface $file_repository,
        private RegionRepositoryInterface $region_repository,
        private DistrictRepositoryInterface $district_repository
    )
    {}

    public function execute(TaxPayerDTOs $taxPayerDTO, int $fileId, int $authenticatedUserId)
    {
        $file = $this->file_repository->findById($fileId);

        if (!$file) {
            throw new DomainException("الملف مع ال ID [$fileId] غير موجود.");
        }

        $existingUser = $file->user;

        if($existingUser->role !== UserRole::Tax_Payer)
        {
            throw new DomainException("المستخدم المرتبط بالملف ليس مكلف .");
        }

        $actor = $this->user_repository->findById($authenticatedUserId);
        if ($actor && $actor->role !== UserRole::Admin) {
            if (!$actor->department || !$existingUser->department
                || $actor->department->id !== $existingUser->department->id) {
                throw new DomainException("لا يمكنك إنشاء ملف لمكلف في قسم لا تنتمي إليه.");
            }
        }

        $region = $this->region_repository->findById($taxPayerDTO->regionId);
        $district = $this->district_repository->findById($taxPayerDTO->districtId);

        if($district->region->id != $region->id)
        {
            throw new DomainException("الحي المحدد غير مربوط بالمنطقة المحددة.");
        }


        $newTaxPayerFile = new TaxPayer(
            id: null,
            fileId: $file->id,
            tradeName: $taxPayerDTO->tradeName,
            commercialRecord: $taxPayerDTO->commercialRecord,
            activityLicense: $taxPayerDTO->activityLicense,
            tradePict: $taxPayerDTO->tradePict,
            insuranceCard: $taxPayerDTO->insuranceCard,
            propertyDocPict: $taxPayerDTO->propertyDocPict,
            fileType: $taxPayerDTO->getFileType(),
            source: $taxPayerDTO->source,
            region: $region,
            district: $district
        );

        $createdTaxPayerFile = $this->tax_payer_repository->createFileToExistingTaxPayer(
            $newTaxPayerFile,
        );


        if (!$createdTaxPayerFile) {
            throw new DomainException("فشل إنشاء الملف الجديد للمكلف.");
        }

        return [
            'taxPayerInfo' => $createdTaxPayerFile,
            'fileInfo' => $file,
            //'userInfo' => $existingUser->toArray()
        ];
    }
}
