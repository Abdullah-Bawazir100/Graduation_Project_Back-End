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
        $user = $this->user_repository->findById($userId);
        if(!$user)
        {
            throw new DomainException("لا يوجد مستخدم مكلف مع ال ID [$userId].");
        }

        $taxPayerFiles = $this->tax_payer_mobile_repository->getTaxPayerMobileFile($userId);

        if(!$taxPayerFiles)
        {
            return null;
        }

        return $taxPayerFiles;
    }
}

