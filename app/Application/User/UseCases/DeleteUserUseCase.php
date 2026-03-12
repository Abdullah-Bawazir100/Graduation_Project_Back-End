<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Application\User\Services\UserAuthorizationService;

class DeleteUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private UserAuthorizationService $userAuthorizationService
    ) {}

    public function execute(User $actor, int $userId): void
    {
        if(!$this->userAuthorizationService->ensureCanDelete($actor)) {
            throw new \DomainException("Unauthorized action [Only admin can delete users].");

        }
        $this->repository->delete($userId);
    }
}