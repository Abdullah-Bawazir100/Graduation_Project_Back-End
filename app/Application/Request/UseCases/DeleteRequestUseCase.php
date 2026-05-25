<?php

namespace  App\Application\Request\UseCases;

use App\Domain\Request\Repositories\TaxPayerRequestRepositoryInterface;
use DomainException;

class DeleteRequestUseCase
{
    public function __construct(
        private TaxPayerRequestRepositoryInterface $tax_payer_request_repository
    )
    {}

    public function execute(int $requestId)
    {
        $request = $this->tax_payer_request_repository->findRequestById($requestId);
        if(!$request)
        {
            throw new DomainException("لا يوجد طلب مع ال ID [$requestId].");
        }
        $this->tax_payer_request_repository->delete($requestId);
    }
}
