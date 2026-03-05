<?php 

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Interfaces\PasswordHashInterface;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Application\User\DTOs\SignUpDTO;
use App\Domain\User\Enums\UserRole;

class SignUpUseCase {

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private DepartmentRepositoryInterface $departmentRepository,
        private PasswordHashInterface $passwordHash
    ) {}

    public function execute(User $actor , SignUpDTO $signUpDTO): array
    {
        // Check if actor has permission to create users
        if (!in_array($actor->role, [UserRole::Admin, UserRole::Manager])) {
            throw new \DomainException('Unauthorized: Only Admin or Manager can create users.');
        }
        
        // Generate username
        $baseUserName = strtolower(
            trim($signUpDTO->firstName) . '.' . trim($signUpDTO->lastName)
        );
        $userName = $this->generateUniqueUserName($baseUserName);
        
        // Generate default password
        $defaultPassword = '12345678';

        // Check if department exists
        $department = $this->departmentRepository->findById($signUpDTO->departmentID);
        if(!$department) 
        {
            throw new \DomainException('Department with ID [' . $signUpDTO->departmentID . '] not found.' );
        }

        // Create User 
        $user = new User(
            id: null,
            firstName: $signUpDTO->firstName,
            lastName: $signUpDTO->lastName,
            dateOfBirth: null, 
            idCard: '', 
            userName: $userName,
            phone: null, 
            password: $this->passwordHash->hashPassword($defaultPassword),
            createdBy: $actor->id,
            department: $department,
            role: UserRole::Employee, // Default role for new users
            mustChangePassword: true
        );

        $createdUser = $this->userRepository->create($user);

        return [
            'user_id' => $createdUser->id,
            'user' => $createdUser,
            'defaultPassword' => $defaultPassword
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