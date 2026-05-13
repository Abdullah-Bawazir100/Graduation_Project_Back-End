<?php

namespace App\Application\File\UseCases;

use App\Application\File\DTOs\FileDTOs;
use App\Domain\Activity_Type\Repositories\Activity_Type_RepositoryInterface;
use App\Domain\File\Entities\File;
use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\FileStatus\Repositories\FileStatusRepositoryInterface;
use App\Domain\PaymentType\Repositories\PaymentTypeRepositoryInterface;
use App\Domain\Region\Repositories\RegionRepositoryInterface;
use App\Domain\District\Repositories\DistrictRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class CreateFileUseCase
{
    public function __construct(
        private FileRepositoryInterface $file_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private DepartmentRepositoryInterface $department_repository,
        private FileStatusRepositoryInterface $file_status_repository,
        private Activity_Type_RepositoryInterface $activity_type_repository,
        private PaymentTypeRepositoryInterface $payment_type_repository,
        private RegionRepositoryInterface $region_repository,
        private DistrictRepositoryInterface $district_repository,
        private UserRepositoryInterface $user_repository
    ) {}

    public function execute(FileDTOs $dto, int $authenticatedUserId): array
    {
        $creator = $this->user_repository->findById($authenticatedUserId);
        if (!$creator) {
            throw new DomainException("المستخدم الذي قام إنشاء الملف غير موجود.");
        }

        // Retrieve related entities
        $taxPayer = $this->tax_payer_repository->findById($dto->taxPayerId);
        if (!$taxPayer) {
            throw new DomainException("المكلف المحدد غير موجود.");
        }

        $department = $this->department_repository->findById($dto->departmentId);
        if (!$department) {
            throw new DomainException("القسم المحدد غير موجود.");
        }

        $fileStatus = $this->file_status_repository->findById($dto->fileStatusId);
        if (!$fileStatus) {
            throw new DomainException("حالة الملف المحددة غير موجودة.");
        }

        $activityType = $this->activity_type_repository->findById($dto->activityTypeId);
        if (!$activityType) {
            throw new DomainException("نوع النشاط المحدد غير موجود.");
        }

        $paymentType = $this->payment_type_repository->findById($dto->paymentTypeId);
        if (!$paymentType) {
            throw new DomainException("نوع الدفع المحدد غير موجود.");
        }

        $region = $this->region_repository->findById($dto->regionId);
        if (!$region) {
            throw new DomainException("المنطقة المحددة غير موجودة.");
        }

        $district = $this->district_repository->findById($dto->districtId);
        if (!$district) {
            throw new DomainException("الحي المحدد غير موجود.");
        }

        // Create the file entity
        $file = new File(
            id: null,
            taxNumber: $dto->taxNumber,
            inventoryNumber: $dto->inventoryNumber,
            activityStartDate: $dto->activityStartDate,
            docsCount: $dto->docsCount,
            note: $dto->note,
            fullAddress: $region->name .'-' . $district->name,
            taxPayer: $taxPayer,
            department: $department,
            fileStatus: $fileStatus,
            activityType: $activityType,
            paymentType: $paymentType,
            region: $region,
            district: $district,
            creator: $creator
        );


        // Persist the file
        $createdFile = $this->file_repository->create($file);

        if (!$createdFile) {
            throw new DomainException("فشل إنشاء الملف.");
        }

        return [
            'fileInfo' => $createdFile->toArray(),
        ];
    }
}
