<?php

namespace App\Application\TaxPayer\UseCases;

use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\Auth;

class ShowTaxPayerUseCase
{
    public function __construct(
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(int $id)
    {
        $taxPayer = $this->tax_payer_repository->findById($id);

        if  (!$taxPayer) {
            throw new DomainException("دافع الضرائب مع ال ID [{$id}] غير موجود.");
        }

        $userInfo = null;

        if ($taxPayer->userId) {
            $user = $this->user_repository->findById($taxPayer->userId);
            if ($user) {
                $userInfo = $user->toArray();
            }
        }

        return [
            'taxPayerInfo' => $taxPayer,
            'userInfo' => $userInfo
        ];
    }
}
