<?php

namespace App\Application\TaxPayer\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use DomainException;

class FindTaxPayerByUserIDUseCase
{
    public function __construct(
        private UserRepositoryInterface $user_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository
    )
    {}

    public function execute(int $userId)
    {
        $user = $this->user_repository->findById($userId);
        if (!$user) {
            throw new DomainException("لا يوجد مستخدم مع ال ID [{$userId}].");
        }

        if ($user->role->value !== 'Tax_Payer') {
            throw new DomainException("المستخدم مع ال ID [{$userId}] ليس مكلف.");
        }

        $taxPayer = $this->tax_payer_repository->findByUserId($userId);
        if(!$taxPayer) {
            throw new DomainException("لا يوجد مستخدم مكلف مرتبط بالمستخدم ID [{$userId}].");
        }

        return [
            'taxPayer' => $taxPayer,
            'userInfo' => [
                'id' => $user->id,
                'fullName' => $user->firstName . ' ' . $user->lastName,
                'userName' => $user->userName,
                'phone' => $user->phone,
                'idCard' => $user->idCard,
                'image' => $user->image,
                'department' => $user->department,
                'role' => $user->role,
            ]
        ];
    }
}
