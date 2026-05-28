<?php

namespace App\Domain\Request\Repositories;

use App\Domain\Request\Entities\TaxPayerRequest;

interface TaxPayerRequestRepositoryInterface
{
    public function create(TaxPayerRequest $request): TaxPayerRequest;
    public function getPendingRequests(?int $departmentId = null): array;
    public function getConfirmedRequests(?int $departmentId = null): array;
    public function getAllRequests(?int $departmentId = null): array;
    public function getArchivedRequests(?int $departmentId = null): array;
    public function getRejectedRequests(?int $departmentId = null): array;
    public function findRequestById(int $id);
    public function acceptRequest(int $requestId): ?TaxPayerRequest;
    public function archiveRequest(int $requestId);
    public function rejectRequest(int $requestId , ?string $note);
    public function findRequestByUserId(int $userId): ?TaxPayerRequest;
    public function findRequestByIdAndUserId(int $requestId, int $userId): ?TaxPayerRequest;
    public function delete(int $requestId): void;
}
