<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Interfaces\PasswordHashInterface;
use App\Domain\User\Interfaces\TokenServiceInterface;
use DomainException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class ChangePasswordUseCase {

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHashInterface $passwordHash,
        private TokenServiceInterface $tokenService
    )
    {}

    public function execute(User $user , $newPassword) : array{


        if(!$user->mustChangePassword)
        {
            throw new DomainException("تغيير كلمة المرور غير مطلوب.");
        }


        if(Hash::check($newPassword , $user->password))
        {
            throw new DomainException("يجب عليك إختيار كلمة مرور مختلفة عن كلمة المرور الإفتراضية.");
        }

        // Reset the new password
        $newPasswordHashed = $this->passwordHash->hashPassword($newPassword);
        $user->password = $newPasswordHashed;
        $user->mustChangePassword = false;

        $updateUser = $this->userRepository->updatePassword($user->id , $newPasswordHashed , false);

        // Create new token after update the password
        $token = $this->tokenService->generateToken($updateUser);

        return [
            'user' => $updateUser,
            'token' => $token
        ];
    }

}
