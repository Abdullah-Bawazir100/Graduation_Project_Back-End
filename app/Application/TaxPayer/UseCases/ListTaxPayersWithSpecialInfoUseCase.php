<?php

namespace  App\Application\TaxPayer\UseCases;

use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;

class ListTaxPayersWithSpecialInfoUseCase
{
    public function __construct(
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute()
    {
        $taxPayersInfo = $this->tax_payer_repository->getTaxPayersWithSpecialInfo();
        $result = [];
        foreach ($taxPayersInfo as $taxPayer) {
            $userInfo = null;

            if ($taxPayer->userId) {
                $user = $this->user_repository->findById($taxPayer->userId);
                if ($user) {
                    $userInfo = $user; // Store the user object directly instead of converting to array
                }
            }

            $result[] = [
                'taxPayerId' => $taxPayer->id,
                'taxPayerName' => $userInfo->firstName . ' ' . $userInfo->lastName, // Access properties directly from user object
                'taxPayerFileType' => $taxPayer->fileType,
            ];
        }

        return $result;
    }
}
