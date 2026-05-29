<?php

namespace App\Application\ResetPassword\UseCases;

use App\Application\ResetPassword\DTOs\ResetPasswordDTOs;
use App\Domain\ResetPassword\Repositories\ResetPasswordRepositoryInterface;
use DomainException;

class VerifyResetCodeUseCase
{
    public function __construct(
        private ResetPasswordRepositoryInterface $resetPasswordRepository
    ) {}

    public function execute(ResetPasswordDTOs $dto): array
    {
        if (!$dto->userId || !$dto->code) {
            throw new DomainException('userId و code مطلوبان');
        }

        $reset = $this->resetPasswordRepository->verifyCode(
            $dto->userId,
            $dto->code
        );

        if (!$reset) {
            throw new DomainException('رمز التحقق غير صحيح أو منتهي الصلاحية');
        }

        return [
            'message' => 'تم التحقق من الكود بنجاح',
            'user_id' => $dto->userId
        ];
    }
}
