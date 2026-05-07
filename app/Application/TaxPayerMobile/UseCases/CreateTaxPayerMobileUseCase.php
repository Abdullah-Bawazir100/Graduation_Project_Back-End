<?php

namespace App\Application\TaxPayerMobile\UseCases;

use App\Application\User\DTOs\UserDTO;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Interfaces\PasswordHashInterface;
use App\Infrastructure\Persistence\Eloquent\Repositories\TaxPayerMobileRepository;
use DomainException;

class CreateTaxPayerMobileUseCase
{
    public function __construct(
        private TaxPayerMobileRepository $taxPayerMobileRepository,
        private DepartmentRepositoryInterface $department_repository,
        private PasswordHashInterface $password_hash
    ) {}

    public function execute(UserDTO $userDTO)
    {
        // Check if department exists
        $department = $this->department_repository->findById($userDTO->departmentID);
        if (!$department) {
            throw new DomainException("القسم مع ال ID [{$userDTO->departmentID}] غير موجود.");
        }

        $user = new User(
            id: null,
            firstName: $userDTO->firstName,
            lastName: $userDTO->lastName,
            idCard: $userDTO->idCard ?? '',
            userName: $userDTO->userName,
            phone: $userDTO->phone ?? '',
            image: $userDTO->image ?? '',
            password: $this->password_hash->hashPassword($userDTO->password),
            createdBy: null,
            department: $department,
            role: $userDTO->role,
            mustChangePassword: true
        );

        $createdTaxPayer = $this->taxPayerMobileRepository->create($user);

        return [
            'user' => $createdTaxPayer->toArray(),
        ];
    }
}
