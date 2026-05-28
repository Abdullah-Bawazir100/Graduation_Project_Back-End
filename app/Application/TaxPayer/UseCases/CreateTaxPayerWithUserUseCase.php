<?php

namespace App\Application\TaxPayer\UseCases;

use App\Application\TaxPayer\DTOs\TaxPayerDTOs;
use App\Application\User\DTOs\UserDTO;
use App\Application\User\Services\UploadFileService;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\TaxInformation\Repositories\TaxInformationRepositoryInterface;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Interfaces\PasswordHashInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;
use Exception;

class CreateTaxPayerWithUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $user_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private DepartmentRepositoryInterface $department_repository,
        private PasswordHashInterface $password_hash,
    )
    {}

    public function execute(TaxPayerDTOs $taxPayerDTO , UserDTO $userDTO , User $actor)
    {
        $department = $this->department_repository->findById($userDTO->departmentID);
        if (!$department) {
            throw new DomainException("القسم مع ال ID [{$userDTO->departmentID}] غير موجود.");
        }

        if ($actor->role !== UserRole::Admin) {
            if (!$actor->department || $actor->department->id !== $department->id) {
                throw new DomainException("لا يمكنك إضافة مكلف في قسم لا تنتمي إليه.");
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
            tradeName: $taxPayerDTO->tradeName,
            commercialRecord: $taxPayerDTO->commercialRecord,
            activityLicense: $taxPayerDTO->activityLicense,
            tradePict: $taxPayerDTO->tradePict,
            insuranceCard: $taxPayerDTO->insuranceCard,
            propertyDocPict: $taxPayerDTO->propertyDocPict,
            fileType: $taxPayerDTO->getFileType(),
            source: $taxPayerDTO->source
        );

        $createdTaxPayer = $this->tax_payer_repository->create($taxPayer);

        return [
            'taxPayerInfo' => $createdTaxPayer,
            'userInfo' => $createdUser->toArray(),
        ];
    }
}
