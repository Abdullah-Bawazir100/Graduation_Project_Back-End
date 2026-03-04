<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Entities\User;

class DeleteUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function execute(User $actor, int $userId): void
    {
        // يمكنك إضافة تحقق صلاحيات هنا إذا أردت
        $this->repository->delete($userId);
    }
}