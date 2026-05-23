<?php

namespace App\Application\Request\UseCases;

use App\Domain\Request\Repositories\TaxPayerRequestRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class FindRequestByIdUseCase
{
    public function __construct(
        private TaxPayerRequestRepositoryInterface $tax_payer_request_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(int $id)
    {
        $request = $this->tax_payer_request_repository->findRequestById($id);
        if(!$request)
        {
            throw new DomainException("لا يوجد طلب مع ال ID [$id].");
        }
        $user = $this->user_repository->findById($request->userId);
        return [
            'RequestInfo' => $request,
            'UserInfo' => $user->toArray(),
        ];
    }
}
