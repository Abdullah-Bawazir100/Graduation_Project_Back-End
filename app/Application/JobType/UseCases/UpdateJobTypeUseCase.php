<?php

namespace App\Application\JobType\UseCases;

use App\Application\JobType\DTOs\JobTypeDTOs;
use App\Domain\JobType\Entities\JobType;
use App\Domain\JobType\Repositories\JobTypeRepositoryInterface;

class UpdateJobTypeUseCase
{
    public function __construct(
        private JobTypeRepositoryInterface $job_type_repository
    ) {}

    public function execute(int $id, JobTypeDTOs $jobTypeDTOs): JobType
    {
        $jobType = $this->job_type_repository->findById($id);

        if (!$jobType) {
            throw new \Exception("نوع الوظيفة مع ال ID [{$id} غير موجود.");
        }

        $name = $jobTypeDTOs->name ?? $jobType->name;

        if (
            $name !== $jobType->name &&
            $this->job_type_repository->existsByName($name)
        ) {
            throw new \DomainException("نوع الوظيفة مع الأسم [ $name} ]موجود بالفعل.");
        }

        return $this->job_type_repository->update(
            new JobType($id, $name)
        );
    }
}
