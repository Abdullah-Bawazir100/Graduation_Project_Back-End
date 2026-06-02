<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\ResetPassword\Entities\ResetPassword;
use App\Domain\ResetPassword\Repositories\ResetPasswordRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\ResetPasswordModel;

class ResetPasswordRepository implements ResetPasswordRepositoryInterface
{
    public function create(ResetPassword $resetPassword): ResetPassword
    {
        $model = ResetPasswordModel::create([
            'user_id'    => $resetPassword->userId,
            'code'       => $resetPassword->code,
        ]);

        return $this->mapToDomain($model);
    }

    public function verifyCode(int $userId, string $code): ?ResetPassword
    {
        $model = ResetPasswordModel::where('user_id', $userId)
            ->where('code', $code)
            ->first();

        return $model ? $this->mapToDomain($model) : null;
    }

    public function deleteByUserId(int $userId): void
    {
        ResetPasswordModel::where('user_id', $userId)->delete();
    }

    public function findLatestByUserId(int $userId): ?ResetPassword
    {
        $model = ResetPasswordModel::where('user_id', $userId)
            ->latest('created_at')
            ->first();

        return $model ? $this->mapToDomain($model) : null;
    }

    private function mapToDomain(ResetPasswordModel $model): ResetPassword
    {
        return new ResetPassword(
            id: $model->id,
            userId: $model->user_id,
            code: $model->code,
            createdAt: $model->created_at,
        );
    }
}
