<?php

namespace App\Application\Request\UseCases;

use App\Domain\Request\Repositories\TaxPayerRequestRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;

class ListPendingRequestsUseCase
{
    public function __construct(
        private TaxPayerRequestRepositoryInterface $tax_payer_request_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(): array
    {
        $pendingRequests = $this->tax_payer_request_repository->getPendingRequests();
        
        $response = [];
        foreach ($pendingRequests as $request) {
            $user = $this->user_repository->findById($request->userId);
            
            $response[] = [
                'RequestInfo' => $request,
                'UserInfo' => $user ? $user->toArray() : null,
            ];
        }

        return $response;
    }
}
