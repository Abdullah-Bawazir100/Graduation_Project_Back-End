<?php

namespace App\Application\JobType\UseCases;

use App\Domain\JobType\Entities\JobType;
use App\Domain\JobType\Repositories\JobTypeRepositoryInterface;
use DomainException;

class ShowJobTypeUseCase
{
    public function __construct(private JobTypeRepositoryInterface $job_type_repository) {}

    public function execute(int $id): ?JobType
    {
        $jobType = $this->job_type_repository->findById($id);

        if (!$jobType) {
            throw new DomainException("نوع الوظيفة مع ال ID [{$id}] غير موجود.");
        }

        return $jobType;
    }
}
