<?php

namespace App\Application\TaxInformation\UseCases;

use App\Domain\TaxInformation\Repositories\TaxInformationRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class ShowTaxInformationUseCase
{
    public function __construct(
        private TaxInformationRepositoryInterface $tax_information_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository
    ) {
    }

    public function execute(int $id)
    {
        $taxInfo = $this->tax_information_repository->findById($id);
        if (!$taxInfo) {
            throw new DomainException("لا توجد معلومات ضريبة مع ال ID [$id].");
        }

        $taxPayer = null;
        $taxPayerUserInfo = null;

        if (!empty($taxInfo->tax_payer_id)) {
            $taxPayer = $this->tax_payer_repository->findById($taxInfo->tax_payer_id);

            if ($taxPayer && !empty($taxPayer->userId)) {
                $user = $this->user_repository->findById($taxPayer->userId);
                if ($user) {
                    $taxPayerUserInfo = $user;
                }
            }
        }

        return [
            'taxInfo' => $taxInfo,
            'taxPayerInfo' => $taxPayer,
            'userInfo' => $taxPayerUserInfo ? $taxPayerUserInfo->toArray() : null,
        ];
    }
}
