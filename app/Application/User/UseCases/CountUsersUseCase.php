<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;

class CountUsersUseCase
{
    public function __construct(
        private UserRepositoryInterface $repository
    ) {}

    public function execute(): int
    {
        return $this->repository->countUsers();
    }
}