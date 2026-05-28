<?php

namespace App\Application\CharitableCompany\UseCases;

use App\Application\CharitableCompany\DTOs\CharitableCompanyDTOs;
use App\Application\CharitableCompany\Mapper\CharitableCompanyMapper;
use App\Application\TaxPayer\DTOs\TaxPayerDTOs;
use App\Application\User\DTOs\UserDTO;
use App\Domain\CharitableCompany\Entities\CharitableCompany;
use App\Domain\CharitableCompany\Repositories\CharitableCompanyRepositoryInterface;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Interfaces\PasswordHashInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\CharitableCompanyModel;
use DomainException;

class CreateCharitableCompanyUseCase
{
    public function __construct(
        private CharitableCompanyRepositoryInterface $charitable_company_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private DepartmentRepositoryInterface $department_repository,
        private PasswordHashInterface $password_hash,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(CharitableCompanyDTOs $charitableCompanyDTOs , TaxPayerDTOs $taxPayerDTOs ,
                            UserDTO $userDTO , User $actor)
    {
        $department = $this->department_repository->findById($userDTO->departmentID);
        if(!$department)
        {
            throw new DomainException("لا يوجد قسم مع ال ID [{$userDTO->departmentID}].");
        }

        if ($actor->role !== UserRole::Admin) {
            if ((int)$actor->department->id !== (int)$department->id) {
                throw new DomainException('غير مصرح لك بإنشاء ملف شركة  خيرية في قسم غير القسم الذي تعمل فيه.');
            }
        }

        $userName = $userDTO->phone;
        $defaultPassword = '12345678';

        $user = new User(
            id: null,
            firstName: $userDTO->firstName,
            lastName: $userDTO->lastName,
            idCard: $userDTO->idCard ?? '',
            userName: $userName,
            phone: $userDTO->phone ?? '',
            image: $userDTO->image ?? '',
            password: $this->password_hash->hashPassword($defaultPassword),
            createdBy: $actor->id,
            department: $department,
            role: $userDTO->getRole(),
            mustChangePassword: true
        );

        $createdUser = $this->user_repository->create($user);

        $taxPayer = new TaxPayer(
            id: null,
            userId: $createdUser->id,
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
            byLawsCopy: $charitableCompanyDTOs->byLawsCopy
        );

        $createdCharitableCompany = $this->charitable_company_repository->create($charitableCompany);

        return [
            'charitableCompanyInfo' => CharitableCompanyMapper::toArray($createdCharitableCompany),
            'taxPayerInfo' => $createdTaxPayer,
            'userInfo' => $createdUser
        ];
    }
}
