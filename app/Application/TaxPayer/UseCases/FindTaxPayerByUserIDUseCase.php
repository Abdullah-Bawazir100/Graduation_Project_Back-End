<?php

namespace App\Application\TaxPayer\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class FindTaxPayerByUserIDUseCase
{
    public function __construct(
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(int $userId)
    {
        $taxPayer = $this->user_repository->findTaxPayerById($userId);
        if(!$taxPayer){
            throw new DomainException("لا يوجد مستخدم دافع للضرائب مع ال ID [{$userId}].");
        }
        return $taxPayer;
    }
}
