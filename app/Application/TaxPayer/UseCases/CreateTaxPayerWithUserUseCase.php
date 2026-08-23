<?php

namespace App\Application\TaxPayer\UseCases;

use App\Application\TaxPayer\DTOs\TaxPayerDTOs;
use App\Application\User\DTOs\UserDTO;
use App\Application\User\Services\UploadFileService;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\TaxInformation\Repositories\TaxInformationRepositoryInterface;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;
use App\Domain\Region\Repositories\RegionRepositoryInterface;
use App\Domain\District\Repositories\DistrictRepositoryInterface;
use App\Domain\User\Interfaces\PasswordHashInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Jobs\SendWhatsAppMessageJob;
use DomainException;
use Exception;
use Illuminate\Support\Str;


class CreateTaxPayerWithUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $user_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private DepartmentRepositoryInterface $department_repository,
        private PasswordHashInterface $password_hash,
        private FileRepositoryInterface $file_repository,
        private RegionRepositoryInterface $region_repository,
        private DistrictRepositoryInterface $district_repository,
    )
    {}

    public function execute(TaxPayerDTOs $taxPayerDTO , UserDTO $userDTO , User $actor)
    {
        if (!$taxPayerDTO->fileId) {
            throw new DomainException("عذراً، يجب فتح ملف للمكلف أولاً قبل إضافة بياناته.");
        }

        $file = $this->file_repository->findById($taxPayerDTO->fileId);
        if (!$file) {
            throw new DomainException("عذراً، لم يتم العثور على ملف لهذا المكلف. يجب فتح ملف أولاً.");
        }

        $department = $this->department_repository->findById($userDTO->departmentID);
        if (!$department) {
            throw new DomainException("القسم مع ال ID [{$userDTO->departmentID}] غير موجود.");
        }

        if ($actor->role !== UserRole::Admin) {
            if ($actor->department->id !== $department->id) {
                throw new DomainException("لا يمكنك إضافة مكلف في قسم لا تنتمي إليه.");
            }
        }

        $userName = $userDTO->phone;
        $generatedPassword = Str::random(8);

        $user = new User(
            id: null,
            firstName: $userDTO->firstName,
            lastName: $userDTO->lastName,
            idCard: $userDTO->idCard ?? '',
            userName: $userName,
            phone: $userDTO->phone ?? '',
            image: $userDTO->image ?? '',
            password: $this->password_hash->hashPassword($generatedPassword),
            createdBy: $actor->id,
            department: $department,
            role: $userDTO->getRole(),
            mustChangePassword: true
        );

        $createdUser = $this->user_repository->create($user);

        $region = null;
        $district = null;
        if ($taxPayerDTO->regionId && $taxPayerDTO->districtId) {
            $region = $this->region_repository->findById($taxPayerDTO->regionId);
            $district = $this->district_repository->findById($taxPayerDTO->districtId);

            if ($district && $region && $district->region->id != $region->id) {
                throw new DomainException("الحي المحدد غير مربوط بالمنطقة المحددة.");
            }
        }

        $taxPayer = new TaxPayer(
            id: null,
            fileId: $taxPayerDTO->fileId,
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

        $createdTaxPayer = $this->tax_payer_repository->create($taxPayer);

        $message = "مرحباً بك في نظام خدمات المكلفين.\n";
        $message .= "تم إنشاء حساب لك بنجاح، بيانات الدخول الخاصة بك:\n\n";
        $message .= "اسم المستخدم: {$userName}\n";
        $message .= "كلمة المرور: {$generatedPassword}\n\n";
        $message .= "يمكنك تغيير كلمة المرور الخاصة بك من حسابك الشخصي عبر تطبيق الموبايل.";

        SendWhatsAppMessageJob::dispatch($userDTO->phone, $message);

        return [
            'taxPayerInfo' => $createdTaxPayer,
            'userInfo' => $createdUser->toArray(),
        ];
    }
}
