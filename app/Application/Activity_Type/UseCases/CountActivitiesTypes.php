<?php

namespace App\Application\Activity_Type\UseCases;

use App\Domain\Activity_Type\Repositories\Activity_Type_RepositoryInterface;

class CountActivitiesTypes
{
    public function __construct(
        private Activity_Type_RepositoryInterface $repository
    ) {}

    public function execute(): int
    {
        return $this->repository->countActivitiesTypes();
    }
}