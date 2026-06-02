<?php

namespace App\Application\ResetPassword\UseCases;

use App\Application\ResetPassword\DTOs\ResetPasswordDTOs;
use App\Domain\ResetPassword\Entities\ResetPassword;
use App\Domain\ResetPassword\Repositories\ResetPasswordRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class ResendResetCodeUseCase
{
    private const RESEND_COOLDOWN_SECONDS = 60;

    public function __construct(
        private ResetPasswordRepositoryInterface $reset_password_repository,
        private UserRepositoryInterface $user_repository
    )
    {
    }

    public function execute(ResetPasswordDTOs $resetPasswordDTOs)
    {
        $user = $this->user_repository->findByUserName($resetPasswordDTOs->userName);

        if (!$user) {
            throw new DomainException('المستخدم غير موجود');
        }

        // التحقق من وقت الانتظار قبل إعادة الإرسال
        $lastReset = $this->reset_password_repository->findLatestByUserId($user->id);
        if ($lastReset && $lastReset->createdAt) {
            $lastResetTime = \Carbon\Carbon::parse($lastReset->createdAt);
            $secondsSinceLastSend = $lastResetTime->diffInSeconds(now());
            
            if ($secondsSinceLastSend < self::RESEND_COOLDOWN_SECONDS) {
                $remainingSeconds = (int) (self::RESEND_COOLDOWN_SECONDS - $secondsSinceLastSend);
                throw new DomainException("يرجى الانتظار {$remainingSeconds} ثانية قبل إعادة إرسال الرمز");
            }
        }

        // حذف الرمز القديم
        $this->reset_password_repository->deleteByUserId($user->id);

        // إنشاء رمز جديد
        $code = random_int(1000, 9999);

        $resetPassword = new ResetPassword(
            id: null,
            userId: $user->id,
            code: (string) $code,
        );

        $this->reset_password_repository->create($resetPassword);

        // TODO: الربط مع خدمة الـ SMS لإرسال الرمز
        // SmsService::send($user->phone, "رمز التحقق الخاص بك هو: $code");

        return [
            'message' => 'تم إعادة إرسال رمز التحقق',
            'user_id' => $user->id,
            'code' => $code
        ];
    }
}
