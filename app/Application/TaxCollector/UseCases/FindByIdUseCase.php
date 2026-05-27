<?php

namespace App\Application\TaxCollector\UseCases;

use App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface;
use App\Domain\TaxCollector\Entities\TaxCollector;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;
use DomainException;

class FindByIdUseCase
{
    public function __construct(
        private TaxCollectorRepositoryInterface $taxCollectorRepository
    ) {}

    public function execute(User $actor, int $id): ?TaxCollector
    {
        $taxCollector = $this->taxCollectorRepository->findById($id);

        if (!$taxCollector) {
            throw new DomainException('المأمور مع ال ID [' . $id . '] غير موجود.');
        }

        $isAdmin = $actor->role === UserRole::Admin;

        if (!$isAdmin && (int)$taxCollector->deptID !== (int)$actor->department->id) {
            throw new DomainException('غير مصرح لك بعرض بيانات مأمور من قسم آخر.');
        }

        return $taxCollector;
    }
}
