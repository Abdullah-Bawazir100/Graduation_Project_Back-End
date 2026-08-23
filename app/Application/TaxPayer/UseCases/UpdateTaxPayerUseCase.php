<?php

namespace  App\Application\TaxPayer\UseCases;

use App\Application\TaxPayer\DTOs\TaxPayerDTOs;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\Region\Repositories\RegionRepositoryInterface;
use App\Domain\District\Repositories\DistrictRepositoryInterface;
use DomainException;

class UpdateTaxPayerUseCase
{
    public function __construct(
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository,
        private FileRepositoryInterface $file_repository,
        private RegionRepositoryInterface $region_repository,
        private DistrictRepositoryInterface $district_repository,
    )
    {}

    public function execute(TaxPayerDTOs $taxPayerDTO , int $id , int $authenticatedUserId)
    {
        $actor = $this->user_repository->findById($authenticatedUserId);
        $isAdmin = $actor->role === UserRole::Admin;

        $existingTaxPayer = $this->tax_payer_repository->findById($id);
        if(!$existingTaxPayer)
        {
            throw new DomainException("المكلف مع ال ID [{$id}] غير موجود.");
        }

        $fileId = $taxPayerDTO->fileId ?? $existingTaxPayer->fileId;
        if (!$fileId) {
             throw new DomainException("يجب تحديد ملف للمكلف.");
        }
        $file = $this->file_repository->findById($fileId);
        if (!$file) {
            throw new DomainException("عذراً، لم يتم العثور على ملف لهذا المكلف.");
        }
        $existingUser = $file->user;

        if (!$isAdmin) {
            $actorDeptId = (int)$actor->department->id;
            
            $oldFile = $this->file_repository->findById($existingTaxPayer->fileId);
            $oldUser = $oldFile ? $oldFile->user : null;

            if ($oldUser && $actorDeptId !== (int)$oldUser->department->id) {
                throw new DomainException('غير مصرح لك بتحديث بيانات مكلف من قسم غير القسم الذي تعمل فيه.');
            }

            if ($existingUser && $actorDeptId !== (int)$existingUser->department->id) {
                throw new DomainException('غير مصرح لك بنقل المكلف إلى قسم غير القسم الذي تعمل فيه.');
            }
        }


        $region = $existingTaxPayer->region;
        $district = $existingTaxPayer->district;

        if ($taxPayerDTO->regionId && $taxPayerDTO->districtId) {
            $region = $this->region_repository->findById($taxPayerDTO->regionId);
            $district = $this->district_repository->findById($taxPayerDTO->districtId);

            if ($district && $region && $district->region->id != $region->id) {
                throw new DomainException("الحي المحدد غير مربوط بالمنطقة المحددة.");
            }
        }

        $taxPayer = new TaxPayer(
            id: $id,
            fileId: $fileId,
            tradeName: $taxPayerDTO->tradeName,
            commercialRecord: $taxPayerDTO->commercialRecord,
            activityLicense: $taxPayerDTO->activityLicense,
            tradePict: $taxPayerDTO->tradePict,
            insuranceCard: $taxPayerDTO->insuranceCard,
            propertyDocPict: $taxPayerDTO->propertyDocPict,
            fileType: $taxPayerDTO->fileType,
            source: $taxPayerDTO->source,
            region: $region,
            district: $district
        );
        $updatedTaxPayer = $this->tax_payer_repository->update($taxPayer, $id);
        return [
            'TaxPayerInfo' => $updatedTaxPayer,
            'userInfo' => $existingUser,
        ];
    }
}
