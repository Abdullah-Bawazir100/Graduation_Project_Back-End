<?php

namespace App\Application\TaxCollector\UseCases;

use App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface;
use App\Application\TaxCollector\DTOs\TaxCollectorDTOs;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\JobType\Repositories\JobTypeRepositoryInterface;
use App\Domain\TaxCollector\Entities\TaxCollector;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;
use DomainException;

class CreateTaxCollectorUseCase
{
    public function __construct(
        private TaxCollectorRepositoryInterface $taxCollectorRepository,
        private JobTypeRepositoryInterface $job_type_repository,
        private DepartmentRepositoryInterface $department_repository
    ) {}

    public function execute(User $actor, TaxCollectorDTOs $dto): TaxCollector
    {
        $isAdmin = $actor->role === UserRole::Admin;

        // Non-admin can only add tax collectors to their own department
        if (!$isAdmin && (int)$actor->department->id !== $dto->deptID) {
            throw new DomainException('غير مصرح لك بإضافة مأمور لقسم غير القسم الذي تعمل فيه.');
        }
        $jobType = $this->job_type_repository->findById($dto->jobTypeId);
        if(!$jobType)
        {
            throw new DomainException("لا يوجد نوع وظيفة مع ال ID [{$dto->jobTypeId}].");
        }

        $department = $this->department_repository->findById($dto->deptID);
        if(!$department)
        {
            throw new DomainException("لا يوجد قسم مع ال ID [{$dto->jobTypeId}].");
        }
        
        $taxCollector = new TaxCollector(
            id: null,
            fullName: $dto->fullName,
            idCard: $dto->idCard,
            phone: $dto->phone,
            jobTypeId: $dto->jobTypeId,
            deptID: $dto->deptID
        );

        return $this->taxCollectorRepository->create($taxCollector);
    }
}
