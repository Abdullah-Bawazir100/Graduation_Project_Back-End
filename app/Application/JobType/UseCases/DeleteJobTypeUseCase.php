<?php

namespace App\Application\JobType\UseCases;

use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\JobType\Repositories\JobTypeRepositoryInterface;

class DeleteJobTypeUseCase
{
    public function __construct(private JobTypeRepositoryInterface $job_type_repository)
    {
    }

    public function execute(int $id): void
    {
        $jobType = $this->job_type_repository->findById($id);

        if (!$jobType) {
            throw new \DomainException("نوع الوظيفة مع ال ID [{$id}] غير موجود.");
        }

        $this->job_type_repository->delete($id);
    }
}

