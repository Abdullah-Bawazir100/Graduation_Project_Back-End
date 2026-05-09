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
        private UserRepository $user_repository,
        private TaxPayerMobileRepositoryInterface $tax_payer_mobile_repository,
        private DepartmentRepositoryInterface $department_repository,
        private PasswordHashInterface $passwordHash
    )
    {}

    public function execute(UserDTO $userDTO)
    {

        $department = $this->department_repository->findById($userDTO->departmentID);
        if(!$department)
        {
            throw new DomainException("القسم مع ال ID [$userDTO->departmentID] غير موجود.");
        }

        $user = $this->user_repository->findById($userDTO->id);
        if(!$user)
        {
            throw new DomainException("المستخدم مع ال ID [$userDTO->id] غير موجود.");
        }

        $user = new User(
            id: $userDTO->id,
            firstName: $userDTO->firstName,
            lastName: $userDTO->lastName,
            idCard: $userDTO->idCard,
            userName: $userDTO->userName,
            phone: $userDTO->phone,
            image: $userDTO->image,
            password: $userDTO->password ? $this->passwordHash->hashPassword($userDTO->password) : $user->password,
            createdBy: $userDTO->createdBy,
            department: $department,
            role: $userDTO->role,
            mustChangePassword: false
        );
        $updatedTaxPayer = $this->user_repository->update($user);
        return $updatedTaxPayer->toArray();
    }
}
