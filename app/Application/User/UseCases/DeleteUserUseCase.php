<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Application\User\Services\UserAuthorizationService;
use DomainException;

class DeleteUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserAuthorizationService $userAuthorizationService
    ) {}

    public function execute(User $actor, int $userId): void
    {
        $this->userAuthorizationService->ensureCanDelete($actor);

        $userToDelete = $this->userRepository->findById($userId);
        if(!$userToDelete)
        {
            throw new DomainException('User with ID [' . $userId . '] Not found.');
        }

        $this->userRepository->delete($userId);

    }
}
