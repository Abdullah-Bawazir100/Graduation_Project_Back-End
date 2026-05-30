<?php

namespace App\Application\TaxPayerMobile\UseCases;

use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\TaxPayerMobile\Repositories\TaxPayerMobileRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class ListTaxPayerFileMobileUseCase
{
    public function __construct(
        private TaxPayerMobileRepositoryInterface $tax_payer_mobile_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository
    )
    {
    }

    public function execute(int $userId)
    {
        $taxPayer = $this->tax_payer_repository->findByUserId($userId);
        if(!$taxPayer)
        {
            throw new DomainException("لا يوجد مستخدم مكلف مع ال ID [$userId].");
        }

        return $this->tax_payer_mobile_repository->getTaxPayerMobileFile($taxPayer->userId);
    }
}
