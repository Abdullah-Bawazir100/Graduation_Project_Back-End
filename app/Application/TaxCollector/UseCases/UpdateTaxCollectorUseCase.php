<?php

namespace App\Application\TaxCollector\UseCases;

use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\JobType\Repositories\JobTypeRepositoryInterface;
use App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface;
use App\Application\TaxCollector\DTOs\TaxCollectorDTOs;
use App\Domain\TaxCollector\Entities\TaxCollector;

class UpdateTaxCollectorUseCase
{
    public function __construct(
        private DepartmentRepositoryInterface $departmentRepository,
        private JobTypeRepositoryInterface $jobTypeRepository,
        private TaxCollectorRepositoryInterface $taxCollectorRepository
    ) {}

    public function execute(int $id, TaxCollectorDTOs $dto): TaxCollector
    {

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
