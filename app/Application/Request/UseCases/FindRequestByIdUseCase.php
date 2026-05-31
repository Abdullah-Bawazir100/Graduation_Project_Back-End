<?php

namespace App\Application\Request\UseCases;

use App\Domain\Request\Enums\enRequestStatus;
use App\Domain\Request\Repositories\TaxPayerRequestRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use DomainException;

class FindRequestByIdUseCase
{
    public function __construct(
        private TaxPayerRequestRepositoryInterface $tax_payer_request_repository,
        private UserRepositoryInterface $user_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository
    )
    {}

    public function execute(int $id, ?int $authenticatedUserId = null)
    {
        $request = $this->tax_payer_request_repository->findRequestById($id);
        if(!$request)
        {
            throw new DomainException("لا يوجد طلب مع ال ID [$id].");
        }
        $user = $this->user_repository->findById($request->userId);

        if ($authenticatedUserId !== null) {
            $actor = $this->user_repository->findById($authenticatedUserId);
            if ($actor && $actor->role !== UserRole::Admin) {
                if ((int)$actor->department->id !== (int)$user->department->id) {
                    throw new DomainException('غير مصرح لك بعرض بيانات طلب من قسم غير القسم الذي تعمل فيه.');
                }
            }
        }

        $taxPayer = null;
        $taxPayerId = null;
        if($request->requestStatus === enRequestStatus::Confirmed)
        {
            $taxPayer = $this->tax_payer_repository->findByUserId($user->id);
            $taxPayerId = $taxPayer->id;
        }

        return [
            'taxPayerId' => $taxPayerId,
            'RequestInfo' => $request,
            'UserInfo' => $user->toArray(),
        ];
    }
}
