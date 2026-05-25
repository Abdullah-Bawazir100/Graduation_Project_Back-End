<?php

namespace App\Domain\Request\Repositories;

use App\Domain\Request\Entities\TaxPayerRequest;

interface TaxPayerRequestRepositoryInterface
{
    public function create(TaxPayerRequest $request): TaxPayerRequest;
    public function getPendingRequests(): array;
    public function getConfirmedRequests(): array;
    public function getAllRequests(): array;
    public function getArchivedRequests(): array;
    public function getRejectedRequests(): array;
    public function findRequestById(int $id);
    public function acceptRequest(int $requestId): ?TaxPayerRequest;
    public function archiveRequest(int $requestId);
    public function rejectRequest(int $requestId , ?string $note);
    public function findRequestByUserId(int $userId): ?TaxPayerRequest;
    public function delete(int $requestId): void;
}
