<?php

namespace App\Application\TaxPayerMobile\UseCases;

use App\Application\User\DTOs\UserDTO;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\TaxPayerMobile\Repositories\TaxPayerMobileRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Interfaces\PasswordHashInterface;
use App\Infrastructure\Persistence\Eloquent\Repositories\UserRepository;
use DomainException;

class UpdateTaxPayerMobileUseCase
{
    public function __construct(
        private UserRepository $taxPayerMobileRepository,
        private TaxPayerMobileRepositoryInterface $tax_payer_mobile_repository,
        private DepartmentRepositoryInterface $department_repository,
        private PasswordHashInterface $passwordHash
    )
    {}

    public function execute(UserDTO $userDTO)
    {

        $department = $this->department_repository->findById($userDTO->departmentID);
        $taxPayer = $this->taxPayerMobileRepository->findById($userDTO->id);

        if (!$taxPayer)
        {
            throw new DomainException("لا يوجد مكلف مع ال ID [{$userDTO->id}].");
        }
        $user = new User(
            id: $userDTO->id,
            firstName: $userDTO->firstName,
            lastName: $userDTO->lastName,
            idCard: $userDTO->idCard ?? '',
            userName: $userDTO->userName,
            phone: $userDTO->phone ?? '',
            image: $userDTO->image ?? '',
            password: $this->passwordHash->hashPassword($userDTO->password),
            createdBy: null,
            department: $department,
            role: $userDTO->role,
            mustChangePassword: false
        );
        $updatedTaxPayer = $this->tax_payer_mobile_repository->update($user);
        return [
            'updatedTaxPayer' => $updatedTaxPayer->toArray()
        ];

    }
}
