<?php

namespace App\Application\ResetPassword\UseCases;

use App\Application\ResetPassword\DTOs\ResetPasswordDTOs;
use App\Domain\ResetPassword\Repositories\ResetPasswordRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Interfaces\PasswordHashInterface;
use DomainException;

class ResetPasswordUseCase
{
    public function __construct(
        private ResetPasswordRepositoryInterface $resetPasswordRepository,
        private UserRepositoryInterface $userRepository,
        private PasswordHashInterface $passwordHash
    ) {}

    public function execute(ResetPasswordDTOs $dto): array
    {
        if (!$dto->userId || !$dto->code || !$dto->newPassword) {
            throw new DomainException('البيانات غير مكتملة');
        }

        $reset = $this->resetPasswordRepository->verifyCode(
            $dto->userId,
            $dto->code
        );

        if (!$reset) {
            throw new DomainException('رمز التحقق غير صحيح أو منتهي الصلاحية');
        }

        $hashedPassword = $this->passwordHash->hashPassword(
            $dto->newPassword
        );

        $this->userRepository->forgetPassword(
            $dto->userId,
            $hashedPassword
        );

        return [
            'message' => 'تم تغيير كلمة المرور بنجاح'
        ];
    }
}
