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
use App\Infrastructure\Persistence\Eloquent\Models\UserModel;
use DomainException;
use Illuminate\Support\Facades\Hash;

class CreateTaxPayerWithUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $user_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private DepartmentRepositoryInterface $department_repository,
        private PasswordHashInterface $password_hash,
        private TaxInformationRepositoryInterface $tax_information_repository
    )
    {}

    public function execute(TaxPayerDTOs $taxPayerDTO , UserDTO $userDTO , User $actor)
    {
        $department = $this->department_repository->findById($userDTO->departmentID);
        if (!$department) {
            throw new DomainException("القسم مع ال ID [{$userDTO->departmentID}] غير موجود.");
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
        );

        $createdTaxPayer = $this->tax_payer_repository->create($taxPayer);

        return [
            'user_id' => $createdTaxPayer->id,
            'userName' => $userName,
            'temporaryPassword' => $defaultPassword,
            'mustChangePassword' => true,
            'user' => $createdTaxPayer
        ];
    }
}
