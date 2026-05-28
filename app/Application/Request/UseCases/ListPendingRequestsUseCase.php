<?php

namespace App\Application\Request\UseCases;

use App\Domain\Request\Repositories\TaxPayerRequestRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Enums\UserRole;

class ListPendingRequestsUseCase
{
    public function __construct(
        private TaxPayerRequestRepositoryInterface $tax_payer_request_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(int $authenticatedUserId): array
    {
        $actor = $this->user_repository->findById($authenticatedUserId);
        $departmentId = ($actor && $actor->role !== UserRole::Admin) ? (int)$actor->department->id : null;

        $pendingRequests = $this->tax_payer_request_repository->getPendingRequests($departmentId);
        
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
