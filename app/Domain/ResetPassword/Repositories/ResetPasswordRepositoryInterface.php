<?php

namespace App\Domain\ResetPassword\Repositories;

use App\Domain\ResetPassword\Entities\ResetPassword;

interface ResetPasswordRepositoryInterface
{
    public function create(ResetPassword $resetPassword): ResetPassword;
    public function verifyCode(int $userId, string $code): ?ResetPassword;
    public function deleteByUserId(int $userId): void;
}
