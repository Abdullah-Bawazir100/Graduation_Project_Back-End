<?php

namespace App\Application\Company\UseCases;

use App\Application\Company\DTOs\CompanyDTOs;
use App\Application\Company\Mapper\CompanyMapper;
use App\Application\TaxPayer\DTOs\TaxPayerDTOs;
use App\Application\User\DTOs\UserDTO;
use App\Domain\Company\Entities\Company;
use App\Domain\Company\Repositories\CompanyRepositoryInterface;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Interfaces\PasswordHashInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Region\Repositories\RegionRepositoryInterface;
use App\Domain\District\Repositories\DistrictRepositoryInterface;
use DomainException;
use Illuminate\Support\Str;
use App\Jobs\SendWhatsAppMessageJob;

class CreateCompanyUseCase
{
    public function __construct(
        private CompanyRepositoryInterface $company_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private DepartmentRepositoryInterface $department_repository,
        private PasswordHashInterface $password_hash,
        private UserRepositoryInterface $user_repository,
        private FileRepositoryInterface $file_repository,
        private RegionRepositoryInterface $region_repository,
        private DistrictRepositoryInterface $district_repository
    )
    {}

    public function execute(CompanyDTOs $companyDTOs , TaxPayerDTOs $taxPayerDTOs ,
                            UserDTO $userDTO , User $actor)
    {
        if (!$taxPayerDTOs->fileId) {
            throw new DomainException("عذراً، يجب فتح ملف للمكلف أولاً قبل إضافة بياناته.");
        }

        $file = $this->file_repository->findById($taxPayerDTOs->fileId);
        if (!$file) {
            throw new DomainException("عذراً، لم يتم العثور على ملف لهذا المكلف. يجب فتح ملف أولاً.");
        }

        $department = $this->department_repository->findById($userDTO->departmentID);
        if(!$department)
        {
            throw new DomainException("لا يوجد قسم مع ال ID [{$userDTO->departmentID}].");
        }

        if ($actor->role !== UserRole::Admin) {
            if ((int)$actor->department->id !== (int)$department->id) {
                throw new DomainException('غير مصرح لك بإنشاء ملف شركة في قسم غير القسم الذي تعمل فيه.');
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

        if ($taxPayerDTOs->regionId && $taxPayerDTOs->districtId) {
            $region = $this->region_repository->findById($taxPayerDTOs->regionId);
            $district = $this->district_repository->findById($taxPayerDTOs->districtId);

            if ($district && $region && $district->region->id != $region->id) {
                throw new DomainException("الحي المحدد غير مربوط بالمنطقة المحددة.");
            }
        }

        $taxPayer = new TaxPayer(
            id: null,
            fileId: $taxPayerDTOs->fileId,
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

        $company = new Company(
            id: null,
            tax_payer_id: $createdTaxPayer->id,
            articlesOfIncorporation: $companyDTOs->articlesOfIncorporation,
            govemorLicense: $companyDTOs->govemorLicense,
            partnersIDCards: $companyDTOs->partnersIDCards,
        );

        $createdCompany = $this->company_repository->create($company);

        $message = "مرحباً بك في نظام خدمات المكلفين.\n";
        $message .= "تم إنشاء حسابك بنجاح، بيانات الدخول الخاصة بك:\n\n";
        $message .= "اسم المستخدم: {$userName}\n";
        $message .= "كلمة المرور: {$generatedPassword}\n\n";
        $message .= "يمكنك تغيير إسم المستخدم و كلمة المرور الخاصة بك من حسابك الشخصي عبر تطبيق الموبايل.";

        SendWhatsAppMessageJob::dispatch($userDTO->phone, $message);

        return [
            'fileInfo' => $file,
            'companyInfo' => CompanyMapper::toArray($createdCompany),
            'taxPayerInfo' => $createdTaxPayer,
            'userInfo' => $createdUser,
        ];
    }
}
