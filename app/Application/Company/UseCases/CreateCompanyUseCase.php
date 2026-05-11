<?php

namespace App\Application\Company\UseCases;

use App\Application\Company\DTOs\CompanyDTOs;
use App\Application\Company\Mapper\CompanyMapper;
use App\Application\TaxPayer\DTOs\TaxPayerDTOs;
use App\Application\User\DTOs\UserDTO;
use App\Domain\Company\Entities\Company;
use App\Domain\Company\Repositories\CompanyRepositoryInterface;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Interfaces\PasswordHashInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class CreateCompanyUseCase
{
    public function __construct(
        private CompanyRepositoryInterface $company_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private DepartmentRepositoryInterface $department_repository,
        private PasswordHashInterface $password_hash,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(CompanyDTOs $companyDTOs , TaxPayerDTOs $taxPayerDTOs ,
                            UserDTO $userDTO , User $actor)
    {
        $department = $this->department_repository->findById($userDTO->departmentID);
        if(!$department)
        {
            throw new DomainException("لا يوجد قسم مع ال ID [{$userDTO->departmentID}].");
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
            'companyInfo' => CompanyMapper::toArray($createdCompany),
            'taxPayerInfo' => $createdTaxPayer,
            'userInfo' => $createdUser,
        ];
    }
}
