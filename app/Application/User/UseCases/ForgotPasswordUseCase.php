<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Interfaces\PasswordHashInterface;
use DomainException;

class ForgotPasswordUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHashInterface $passwordHash,
    ) {}

    public function execute(string $userName, string $phone, string $newPassword): void
    {
        $user = $this->userRepository->findByUserName($userName);

        if (!$user) {
            throw new DomainException("البيانات غير صحيحة : إسم المستخدم غير موجود.");
        }

        if ($user->phone !== $phone) {
            throw new DomainException("البيانات غير صحيحة : رقم الهاتف غير موجود.");
        }

        $hashedPassword = $this->passwordHash->hashPassword($newPassword);

        // $this->userRepository->updatePasswordOnly(
        //     $user->id,
        //     $hashedPassword,
        //     false
        // );
    }
}
