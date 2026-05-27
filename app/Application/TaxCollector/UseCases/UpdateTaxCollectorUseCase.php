<?php

namespace App\Application\TaxCollector\UseCases;

use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\JobType\Repositories\JobTypeRepositoryInterface;
use App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface;
use App\Application\TaxCollector\DTOs\TaxCollectorDTOs;
use App\Domain\TaxCollector\Entities\TaxCollector;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;

class UpdateTaxCollectorUseCase
{
    public function __construct(
        private DepartmentRepositoryInterface $departmentRepository,
        private JobTypeRepositoryInterface $jobTypeRepository,
        private TaxCollectorRepositoryInterface $taxCollectorRepository
    ) {}

    public function execute(User $actor, int $id, TaxCollectorDTOs $dto): TaxCollector
    {
        $isAdmin = $actor->role === UserRole::Admin;

        // Fetch existing tax collector
        $existing = $this->taxCollectorRepository->findById($id);
        if (!$existing) {
            throw new \DomainException('المأمور مع ال ID [' . $id . '] غير موجود.');
        }

        if (!$isAdmin) {
            $actorDeptId = (int)$actor->department->id;

            // Non-admin cannot update a tax collector from another department
            if ($actorDeptId !== (int)$existing->deptID) {
                throw new \DomainException('غير مصرح لك بتحديث بيانات مأمور من قسم غير القسم الذي تعمل فيه.');
            }

            // Non-admin cannot transfer a tax collector to another department
            if ($actorDeptId !== (int)$dto->deptID) {
                throw new \DomainException('غير مصرح لك بنقل المأمور إلى قسم غير القسم الذي تعمل فيه.');
            }
        }

        $department = $this->departmentRepository->findById($dto->deptID);
        if (!$department) throw new \DomainException('القسم مع ال ID [' . $dto->deptID . '] غير موجود.');

        $jobType = $this->jobTypeRepository->findById($dto->jobTypeId);
        if (!$jobType) throw new \DomainException('نوع الوظيفة مع ال ID [' . $dto->jobTypeId . '] غير موجود.');

        $taxCollector = new TaxCollector(
            id: $id,
            fullName: $dto->fullName,
            idCard: $dto->idCard,
            phone: $dto->phone,
            jobTypeId: $dto->jobTypeId,
            deptID: $dto->deptID,
        );

        return $this->taxCollectorRepository->update($taxCollector);
    }
}
