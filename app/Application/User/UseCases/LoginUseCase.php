<?php

namespace App\Application\User\UseCases;

use App\Application\User\DTOs\LoginDTO;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Interfaces\TokenServiceInterface;
use App\Domain\User\Interfaces\PasswordHashInterface;
use Illuminate\Support\Facades\Hash;

class LoginUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private TokenServiceInterface $tokenService,
        private PasswordHashInterface $passwordHash
    ) {}

    public function execute(LoginDTO $loginDTO)
    {
        // Check if userName exists
        $user = $this->userRepository->findByUserName($loginDTO->userName);
        if(!$user) {
            throw new \DomainException('Invalid credentials: User not found.');
        }

        // Check password
        if(!$this->passwordHash->verifyPassword($loginDTO->password , $user->password)) {
            throw new \DomainException('Invalid credentials: Incorrect password.');
        }

        $token = $this->tokenService->generateToken($user);

        $mustChangePassword = $user->mustChangePassword || $user->password === '12345678';
        // Check if must change password
        if($mustChangePassword) {

            return [
                'token' => $token,
                'must_change_password' => true
            ];
        }

        // User already change his password & login
        return [
            'user' => $user,
            'token' => $token,
            'must_change_password' => true
        ];
    }
}
