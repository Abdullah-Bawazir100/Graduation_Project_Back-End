<?php

namespace App\Application\JobType\UseCases;

use App\Domain\JobType\Repositories\JobTypeRepositoryInterface;

class CountJobTypesUseCase
{
    public function __construct(
        private JobTypeRepositoryInterface$job_type_repository
    )
    {}

    public function execute(): int
    {
        return $this->job_type_repository->countJobTypes();
    }
}
