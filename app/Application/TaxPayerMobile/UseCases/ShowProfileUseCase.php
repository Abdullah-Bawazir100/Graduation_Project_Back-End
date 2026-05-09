<?php

namespace App\Application\TaxPayerMobile\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;

class ShowProfileUseCase
{
    public function __construct(
        private UserRepositoryInterface $user_repository
    )
    {
    }

    public function execute(int $id)
    {
        $taxPayerProfile = $this->user_repository->findById($id);
        return $taxPayerProfile;
    }
}
