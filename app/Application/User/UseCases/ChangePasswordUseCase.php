<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Interfaces\PasswordHashInterface;
use App\Domain\User\Interfaces\TokenServiceInterface;

class ChangePasswordUseCase {

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHashInterface $passwordHash,
        private TokenServiceInterface $tokenService
    )
    {}

    public function execute(User $user , $newPassword) : array{

        if(!$user->mustChangePassword) {
            throw new \DomainException('Password change is not required for this user.');
        }

        // Reset the new password
        $user->password = $this->passwordHash->hashPassword($newPassword);
        $user->mustChangePassword = false;

        $updateUser = $this->userRepository->update($user);

        // Create new token after update the password
        $token = $this->tokenService->generateToken($updateUser);

        return [
            'user' => $updateUser,
            'token' => $token
        ];
    }
}