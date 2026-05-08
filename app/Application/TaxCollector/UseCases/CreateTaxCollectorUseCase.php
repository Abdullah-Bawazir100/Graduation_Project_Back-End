<?php

namespace App\Application\TaxCollector\UseCases;

use App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface;
use App\Application\TaxCollector\DTOs\TaxCollectorDTOs;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\JobType\Repositories\JobTypeRepositoryInterface;
use App\Domain\TaxCollector\Entities\TaxCollector;
use DomainException;

class CreateTaxCollectorUseCase
{
    public function __construct(
        private TaxCollectorRepositoryInterface $taxCollectorRepository,
        private JobTypeRepositoryInterface $job_type_repository,
        private DepartmentRepositoryInterface $department_repository
    ) {}

    public function execute(TaxCollectorDTOs $dto): TaxCollector
    {
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
