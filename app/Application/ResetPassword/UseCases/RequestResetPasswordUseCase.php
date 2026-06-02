<?php

namespace App\Application\ResetPassword\UseCases;

use App\Application\ResetPassword\DTOs\ResetPasswordDTOs;
use App\Domain\ResetPassword\Entities\ResetPassword;
use App\Domain\ResetPassword\Repositories\ResetPasswordRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class RequestResetPasswordUseCase
{
    public function __construct(
        private ResetPasswordRepositoryInterface $reset_password_repository,
        private UserRepositoryInterface $user_repository
    )
    {
    }

    public function execute(ResetPasswordDTOs  $resetPasswordDTOs)
    {
        $user = $this->user_repository
            ->findByUserName($resetPasswordDTOs->userName);

        if (!$user) {
            throw new DomainException('المستخدم غير موجود');
        }

        $code = random_int(1000, 9999);

        $this->reset_password_repository
            ->deleteByUserId($user->id);

        $resetPassword = new ResetPassword(
            id: null,
            userId: $user->id,
            code: (string) $code,
        );

        $this->reset_password_repository->create($resetPassword);

        // TODO: الربط مع خدمة الـ SMS لإرسال رمز التحقق
        // SmsService::send($user->phone, "رمز التحقق الخاص بك هو: $code");

        return [
            'message' => 'تم إنشاء رمز التحقق',
            'user_id' => $user->id,
            'code' => $code
        ];
    }
}

