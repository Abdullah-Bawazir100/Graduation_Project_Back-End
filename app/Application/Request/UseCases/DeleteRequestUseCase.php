<?php

namespace  App\Application\Request\UseCases;

use App\Domain\Request\Repositories\TaxPayerRequestRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use DomainException;

class DeleteRequestUseCase
{
    public function __construct(
        private TaxPayerRequestRepositoryInterface $tax_payer_request_repository,
        private UserRepositoryInterface $user_repository
    )
    {}

    public function execute(int $requestId, ?int $authenticatedUserId = null)
    {
        $request = $this->tax_payer_request_repository->findRequestById($requestId);
        if(!$request)
        {
            throw new DomainException("لا يوجد طلب مع ال ID [$requestId].");
        }

        if ($authenticatedUserId !== null) {
            $user = $this->user_repository->findById($request->userId);
            $actor = $this->user_repository->findById($authenticatedUserId);
            
            if ($actor && $actor->role !== UserRole::Admin) {
                if ((int)$actor->department->id !== (int)$user->department->id) {
                    throw new DomainException('غير مصرح لك بحذف طلب من قسم غير القسم الذي تعمل فيه.');
                }
            }
        }

        $this->tax_payer_request_repository->delete($requestId);
    }
}
