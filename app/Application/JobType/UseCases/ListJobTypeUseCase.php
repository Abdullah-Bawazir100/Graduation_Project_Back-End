<?php

namespace App\Application\JobType\UseCases;

use App\Domain\JobType\Repositories\JobTypeRepositoryInterface;

class ListJobTypeUseCase
{
    public function __construct(
        private JobTypeRepositoryInterface $job_type_repository
    ) {}

    public function execute()
    {
        return $this->job_type_repository->getAll();
    }
}
