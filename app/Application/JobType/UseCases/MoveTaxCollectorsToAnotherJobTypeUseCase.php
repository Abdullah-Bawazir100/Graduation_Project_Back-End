<?php

namespace  App\Application\JobType\UseCases;

use App\Domain\JobType\Repositories\JobTypeRepositoryInterface;
use DomainException;

class MoveTaxCollectorsToAnotherJobTypeUseCase
{
    public function __construct(
        private JobTypeRepositoryInterface $job_type_repository
    )
    {}

    public function execute(int $oldJobTypeId , int $newJobTypeId)
    {
        $oldJobType = $this->job_type_repository->findById($oldJobTypeId);
        if(!$oldJobType)
        {
            throw new DomainException("نوع الوظيفة القديم غير موجود.");
        }
        $newJobType = $this->job_type_repository->findById($oldJobTypeId);
        if(!$newJobType)
        {
            throw new DomainException("نوع الوظيفة الجديد غير موجود.");
        }
        if($oldJobType === $newJobType)
        {
            throw new DomainException("لا يمكن نقل المأمورين الى نفس نوع الوظيفة.");
        }
        $this->job_type_repository->moveTaxCollectorsToAnotherJobType($oldJobTypeId , $newJobTypeId);
    }
}
