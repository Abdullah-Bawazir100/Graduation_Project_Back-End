<?php

namespace App\Application\Request\UseCases;

use App\Domain\Request\Repositories\TaxPayerRequestRepositoryInterface;
use DomainException;

class ExistsRequestUseCase
{
    public function __construct(
        private TaxPayerRequestRepositoryInterface $tax_payer_request_repository
    ) {}

    public function execute(int $userId): bool
    {
        return $this->tax_payer_request_repository->existsRequest($userId);
    }
}
