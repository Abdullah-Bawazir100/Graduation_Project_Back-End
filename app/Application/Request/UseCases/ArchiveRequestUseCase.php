<?php

namespace App\Application\Request\UseCases;

use App\Application\File\DTOs\FileDTOs;
use App\Domain\Activity_Type\Entities\Activity_Type;
use App\Domain\Activity_Type\Repositories\Activity_Type_RepositoryInterface;
use App\Domain\Department\Entities\Department;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\District\Entities\District;
use App\Domain\District\Repositories\DistrictRepositoryInterface;
use App\Domain\File\Entities\File;
use App\Domain\Request\Entities\TaxPayerRequest;
use App\Domain\Request\Enums\enRequestStatus;
use App\Domain\Request\Repositories\TaxPayerRequestRepositoryInterface;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\FileStatus\Entities\FileStatus;
use App\Domain\FileStatus\Repositories\FileStatusRepositoryInterface;
use App\Domain\PaymentType\Entities\PaymentType;
use App\Domain\PaymentType\Repositories\PaymentTypeRepositoryInterface;
use App\Domain\Region\Entities\Region;
use App\Domain\Region\Repositories\RegionRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class ArchiveRequestUseCase
{
    public function __construct(
        private TaxPayerRequestRepositoryInterface $tax_payer_request_repository,
        private UserRepositoryInterface $user_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private FileRepositoryInterface $file_repository,
        private DepartmentRepositoryInterface $department_repository,
        private FileStatusRepositoryInterface $file_status_repository,
        private Activity_Type_RepositoryInterface $activity_type_repository,
        private PaymentTypeRepositoryInterface $payment_type_repository,
        private RegionRepositoryInterface $region_repository,
        private DistrictRepositoryInterface $district_repository,
    )
    {}

    public function execute(int $requestId , FileDTOs $fileDTOs , int $authenticatedUserId)
    {
        $request =  $this->tax_payer_request_repository->findRequestById($requestId);
        if(!$request)
        {
            throw new DomainException("لا يوجد طلب مع ال ID [$requestId].");
        }

        $creator = $this->user_repository->findById($authenticatedUserId);
        if (!$creator) {
            throw new DomainException("المستخدم الذي قام إنشاء الملف غير موجود.");
        }
        // Retrieve related entities
        $taxPayer = $this->tax_payer_repository->findById($fileDTOs->taxPayerId);
        if (!$taxPayer) {
            throw new DomainException("المكلف المحدد غير موجود.");
        }

        $department = $this->department_repository->findById($fileDTOs->departmentId);
        if (!$department) {
            throw new DomainException("القسم المحدد غير موجود.");
        }

        $fileStatus = $this->file_status_repository->findById($fileDTOs->fileStatusId);
        if (!$fileStatus) {
            throw new DomainException("حالة الملف المحددة غير موجودة.");
        }

        $activityType = $this->activity_type_repository->findById($fileDTOs->activityTypeId);
        if (!$activityType) {
            throw new DomainException("نوع النشاط المحدد غير موجود.");
        }

        $paymentType = $this->payment_type_repository->findById($fileDTOs->paymentTypeId);
        if (!$paymentType) {
            throw new DomainException("نوع الدفع المحدد غير موجود.");
        }

        $region = $this->region_repository->findById($fileDTOs->regionId);
        if (!$region) {
            throw new DomainException("المنطقة المحددة غير موجودة.");
        }

        $district = $this->district_repository->findById($fileDTOs->districtId);
        if (!$district) {
            throw new DomainException("الحي المحدد غير موجود.");
        }

        if (
            !$creator->department ||
            $creator->department->id !== $department->id
        ) {
            throw new DomainException(
                "لا يمكنك إنشاء ملف في قسم لا تنتمي إليه."
            );
        }

        if($request->requestStatus === EnRequestStatus::Archived)
        {
            throw new DomainException("تم ترحيل هذا الملف من قبل.");
        }

        if($request->requestStatus === EnRequestStatus::Confirmed)
        {
            $rejectedRequest = $this->tax_payer_request_repository->archiveRequest($requestId);
            $user = $this->user_repository->findById($request->userId);
            $this->storeRequestToFilesTable($fileDTOs , $creator ,
            $department , $taxPayer , $fileStatus , $activityType , $paymentType , $region , $district);

            return [
                'RequestInfo' => $rejectedRequest,
                'UserInfo'  => $user->toArray()
            ];
        }
        else
        {
            throw new DomainException("لا يمكن ترحيل هذا الطلب لأن حالته الحالية ليست مؤكدة.");
        }
    }

    private function storeRequestToFilesTable(
            FileDTOs $fileDTOs , User $creator ,
            Department $department , TaxPayer $taxPayer , FileStatus $fileStatus ,
            Activity_Type $activityType , PaymentType $paymentType , Region $region , District $district
            )
    {
        $rejectedFile = new File(
            id: null,
            taxNumber: $fileDTOs->taxNumber,
            inventoryNumber: $fileDTOs->inventoryNumber,
            activityStartDate: $fileDTOs->activityStartDate,
            docsCount: $fileDTOs->docsCount,
            note: $fileDTOs->note,
            taxPayer: $taxPayer,
            department: $department,
            fileStatus: $fileStatus,
            activityType: $activityType,
            paymentType: $paymentType,
            region: $region,
            district: $district,
            creator: $creator
        );
        $rejectedFile = $this->file_repository->create($rejectedFile);
    }
}
