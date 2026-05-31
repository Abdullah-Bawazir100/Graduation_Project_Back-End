<?php

namespace App\Application\File\UseCases;

use App\Application\File\DTOs\FileDTOs;
use App\Domain\Activity_Type\Repositories\Activity_Type_RepositoryInterface;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\District\Repositories\DistrictRepositoryInterface;
use App\Domain\File\Entities\File;
use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\FileStatus\Repositories\FileStatusRepositoryInterface;
use App\Domain\PaymentType\Repositories\PaymentTypeRepositoryInterface;
use App\Domain\Region\Repositories\RegionRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use DomainException;

class UpdateFileUseCase
{
    public function __construct(
        private FileRepositoryInterface $file_repository,
        private TaxPayerRepositoryInterface  $tax_payer_repository,
        private DepartmentRepositoryInterface $department_repository,
        private FileStatusRepositoryInterface $file_status_repository,
        private Activity_Type_RepositoryInterface $activity_type_repository,
        private PaymentTypeRepositoryInterface $payment_type_repository,
        private RegionRepositoryInterface  $region_repository,
        private DistrictRepositoryInterface $district_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(FileDTOs $dto , int $id, int $authenticatedUserId): ?File
    {
        $existingFile = $this->file_repository->findById($id);
        $user = $this->user_repository->findById($authenticatedUserId);

        if (!$existingFile) {
            throw new DomainException("لا يوجد ملف مع ال ID [$id].");
        }

        if (!$user) {
            throw new DomainException("المستخدم غير موجود.");
        }

        if ($user->role !== UserRole::Admin) {
            if (!$user->department || $user->department->id !== $existingFile->department->id) {
                throw new DomainException("لا يمكنك تعديل ملف في قسم لا تنتمي إليه.");
            }

            if ($dto->departmentId !== null && $dto->departmentId !== $user->department->id) {
                throw new DomainException("لا يمكنك نقل الملف إلى قسم لا تنتمي إليه.");
            }
        }

        $newTaxPayer = $existingFile->taxPayer;
        if ($dto->taxPayerId !== null) {
            $newTaxPayer = $this->tax_payer_repository->findById($dto->taxPayerId);

            if (!$newTaxPayer) {
                throw new DomainException("لا يوجد مكلف مع ال ID [$dto->taxPayerId].");
            }
        }

        $newDepartment = $existingFile->department;
        if ($dto->departmentId !== null) {
            $newDepartment = $this->department_repository->findById($dto->departmentId);

            if (!$newDepartment) {
                throw new DomainException("لا يوجد قسم مع ال ID [$dto->departmentId].");
            }
        }

        $newFileStatus = $existingFile->fileStatus;
        if ($dto->fileStatusId !== null) {
            $newFileStatus = $this->file_status_repository->findById($dto->fileStatusId);

            if (!$newFileStatus) {
                throw new DomainException("لا يوجد حالة ملف مع ال ID [$dto->fileStatusId].");
            }
        }

        $newActivityType = $existingFile->activityType;
        if ($dto->activityTypeId !== null) {
            $newActivityType = $this->activity_type_repository->findById($dto->activityTypeId);

            if (!$newActivityType) {
                throw new DomainException("لا يوجد نوع نشاط مع ال ID [$dto->activityTypeId].");
            }
        }

        $newPaymentType = $existingFile->paymentType;
        if ($dto->paymentTypeId !== null) {
            $newPaymentType = $this->payment_type_repository->findById($dto->paymentTypeId);

            if (!$newPaymentType) {
                throw new DomainException("لا يوجد نوع دفع مع ال ID [$dto->paymentTypeId].");
            }
        }

        $newRegion = $existingFile->region;
        if ($dto->regionId !== null) {
            $newRegion = $this->region_repository->findById($dto->regionId);

            if (!$newRegion) {
                throw new DomainException("لا توجد منطقة مع ال ID [$dto->regionId].");
            }
        }

        $newDistrict = $existingFile->district;
        if ($dto->districtId !== null) {
            $newDistrict = $this->district_repository->findById($dto->districtId);

            if (!$newDistrict) {
                throw new DomainException("لا يوجد حي مع ال ID [$dto->districtId].");
            }
        }

        $file = new File(
            id: $id,
            taxNumber: $dto->taxNumber ?? $existingFile->taxNumber,
            inventoryNumber: $dto->inventoryNumber ?? $existingFile->inventoryNumber,
            activityStartDate: $dto->activityStartDate ?? $existingFile->activityStartDate,
            docsCount: $dto->docsCount ?? $existingFile->docsCount,
            note: $dto->note ?? $existingFile->note,

            taxPayer: $newTaxPayer,
            department: $newDepartment,
            fileStatus: $newFileStatus,
            activityType: $newActivityType,
            paymentType: $newPaymentType,
            region: $newRegion,
            district: $newDistrict,

            creator: $existingFile->creator,
        );

        return $this->file_repository->update($file, $id);
    }
}
