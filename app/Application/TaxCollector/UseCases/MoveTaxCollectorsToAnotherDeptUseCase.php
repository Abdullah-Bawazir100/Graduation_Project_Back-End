<?php

namespace App\Application\TaxCollector\UseCases;

use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface;
use DomainException;

class MoveTaxCollectorsToAnotherDeptUseCase
{
    public function __construct(
        private TaxCollectorRepositoryInterface $tax_collector_repository,
        private DepartmentRepositoryInterface $department_repository
    ) {}

    public function execute(int $oldDepartmentId, int $newDepartmentId): void
    {
        $oldDepartment = $this->department_repository->findById($oldDepartmentId);
        if(!$oldDepartment){
            throw new DomainException("القسم القديم غير موجود.");
        }
        $newDepartment = $this->department_repository->findById($newDepartmentId);
        if(!$newDepartment){
            throw new DomainException("القسم الجديد غير موجود.");
        }

        if ($oldDepartmentId === $newDepartmentId) {
            throw new DomainException("لا يمكن نقل المستخدمين إلى نفس القسم.");
        }

        $this->tax_collector_repository->moveTaxCollectorsToAnotherDepartment($oldDepartmentId, $newDepartmentId);
    }
}
