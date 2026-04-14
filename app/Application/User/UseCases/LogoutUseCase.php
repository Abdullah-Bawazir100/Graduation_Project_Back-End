<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Interfaces\TokenServiceInterface;

class LogoutUseCase
{
    public function __construct(
        private TokenServiceInterface $tokenServiceInterface
    )
    {}

    public function execute(string $token)
    {
        $this->tokenServiceInterface->removeToken($token);
    }
}
