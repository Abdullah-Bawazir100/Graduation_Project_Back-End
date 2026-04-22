<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;
use App\Application\User\DTOs\UserDTO;
use App\Domain\User\Interfaces\PasswordHashInterface;
use DomainException;

class CreateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private DepartmentRepositoryInterface $departmentRepository,
        private PasswordHashInterface $passwordHash
    ) {}

    /**
     * @param User $actor
     * @return array
     */

    public function execute(User $actor, UserDTO $userDTO)
    {
        if (!in_array($actor->role, [UserRole::Admin, UserRole::Manager])) {
            throw new DomainException('Unauthorized: Only Admin or Manager can create users.');
        }

        // Check if department exists
        $department = $this->departmentRepository->findById($userDTO->departmentID);
        if (!$department) {
            throw new DomainException("Department with ID [{$userDTO->departmentID}] not found.");
        }

        // Generate username
        // $baseUserName = strtolower(
        //     trim($userDTO->firstName) . '.' . trim($userDTO->lastName)
        // );
        // $userName = $this->generateUniqueUserName($baseUserName);


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
            password: $this->passwordHash->hashPassword($defaultPassword),
            createdBy: $actor->id,
            department: $department,
            role: $userDTO->getRole(),
            mustChangePassword: true
        );

        $createdUser = $this->userRepository->create($user);

        return [
            'user_id' => $createdUser->id,
            'userName' => $userName,
            'temporaryPassword' => $defaultPassword,
            'mustChangePassword' => true,
            'user' => $createdUser
        ];
    }


    private function generateUniqueUserName(string $baseUserName): string
    {
        $userName = $baseUserName;
        $counter = 1;

        while ($this->userRepository->findByUserName($userName)) {
            $userName = $baseUserName . $counter;
            $counter++;
        }

        return $userName;
    }

}
