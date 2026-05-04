<?php

namespace App\Application\TaxPayer\UseCases;

use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
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

        $authUser = Auth::user();
        if ($taxPayer->userId !== $authUser->id) {
            throw new DomainException("غير مصرح لك بالإطلاع على بيانات مكلفين آخرين.");
        }

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

        return [
            'taxPayer' => $taxPayer,
            'userInfo' => $userInfo
        ];
    }
}
