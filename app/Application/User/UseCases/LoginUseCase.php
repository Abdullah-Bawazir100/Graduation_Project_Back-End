<?php

namespace App\Domain\User\UseCases;

use App\Application\User\DTOs\LoginDTO;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Interfaces\TokenServiceInterface;
use App\Domain\User\Interfaces\PasswordHashInterface;

class LoginUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRespository,
        private TokenServiceInterface $tokenService,
        private PasswordHashInterface $passwordHash
    ) {}

    public function execute(LoginDTO $loginDTO)
    {
        // Check if userName exists
        $user = $this->userRespository->findByUserName($loginDTO->userName);
        if(!$user) {
            throw new \DomainException('Invalid credentials: User not found.');
        }

        // Check password
        if(!$this->passwordHash->verifyPassword($loginDTO->password, $user->password)) {
            throw new \DomainException('Invalid credentials: Incorrect password.');
        }

        // Check if must change password
        if($user->mustChangePassword) {
            return [
                'user' => $user,
                'token' => null,
                'must_change_password' => true
            ];
        }

        // Create Token
        $token = $this->tokenService->generateToken($user);
        return [
            'user' => $user,
            'token' => $token,
            'must_change_password' => false
        ];
    }
}