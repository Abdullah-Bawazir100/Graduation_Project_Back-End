<?php

namespace App\Application\TaxPayer\UseCases;

use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;

class ListAllTaxPayersWithSourceUseCase
{
    public function __construct(
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute()
    {
        $taxPayers = $this->tax_payer_repository->getAllTaxPayers();

        $result = [];
        foreach ($taxPayers as $taxPayer) {
            $userInfo = null;

            if ($taxPayer->userId) {
                $user = $this->user_repository->findById($taxPayer->userId);
                if ($user) {
                    $userInfo = $user->toArray(); // Return the full user object/array instead of selected fields
                }
            }

            $result[] = [
                'taxPayerInfo' => $taxPayer,
                'userInfo' => $userInfo
            ];
        }

        return $result;
    }
}
