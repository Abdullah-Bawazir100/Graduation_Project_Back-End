<?php

namespace App\Application\TaxPayer\UseCases;

use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;

class ListTaxPayersUseCase
{
    public function __construct(
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute()
    {
        $taxPayers = $this->tax_payer_repository->getAll();

        $result = [];
        foreach ($taxPayers as $taxPayer) {
            $userInfo = null;

            if ($taxPayer->userId) {
                $user = $this->user_repository->findById($taxPayer->userId);
                if ($user) {
                    $userInfo = [
                        'id' => $user->id,
                        'fullName' => $user->firstName . ' ' . $user->lastName,
                        'userName' => $user->userName,
                        'phone' => $user->phone,
                        'role' => $user->role,
                    ];
                }
            }

            $result[] = [
                'taxPayer' => $taxPayer,
                'userInfo' => $userInfo
            ];
        }

        return $result;
    }
}
