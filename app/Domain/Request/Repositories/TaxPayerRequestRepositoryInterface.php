<?php

namespace App\Domain\Request\Repositories;

use App\Domain\Request\Entities\TaxPayerRequest;

interface TaxPayerRequestRepositoryInterface
{
    public function create(TaxPayerRequest $request): TaxPayerRequest;
    public function getPendingRequests(): array;
    public function getConfirmedRequests(): array;
    public function getAllRequests(): array;
    public function findRequestById(int $id);
    public function acceptRequest(int $requestId): ?TaxPayerRequest;
}
