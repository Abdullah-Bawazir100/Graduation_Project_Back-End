<?php

namespace App\Application\JobType\UseCases;

use App\Application\JobType\DTOs\JobTypeDTOs;
use App\Domain\JobType\Entities\JobType;
use App\Domain\JobType\Repositories\JobTypeRepositoryInterface;

class CreateJobTypeUseCase
{

    public function __construct(
        private JobTypeRepositoryInterface $jobTypeRepositoryInterface
    )
    {}

    public function execute(JobTypeDTOs $jobTypeDTOs): JobType
    {
        $name = trim($jobTypeDTOs->name);

        if ($this->jobTypeRepositoryInterface->existsByName($name)) {
            throw new \DomainException("نوع الوظيفة مع الإسم [{$name}] موجود بالفعل.");
        }

        return $this->jobTypeRepositoryInterface->create(
            new JobType(null, $name)
        );
    }
}
