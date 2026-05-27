<?php

namespace App\Application\TaxCollector\UseCases;

use App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;

class ListTaxCollectorUseCase
{
    public function __construct(
        private TaxCollectorRepositoryInterface $taxCollectorRepository
    ) {}

    public function execute(User $actor): array
    {
        $isAdmin = $actor->role === UserRole::Admin;
        $departmentId = $isAdmin ? null : (int)$actor->department->id;

        return $this->taxCollectorRepository->getAll($departmentId);
    }
}
