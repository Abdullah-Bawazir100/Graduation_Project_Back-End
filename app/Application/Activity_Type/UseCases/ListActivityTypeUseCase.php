<?php

namespace App\Application\Activity_Type\UseCases;

use App\Domain\Activity_Type\Repositories\Activity_Type_RepositoryInterface;

class ListActivityTypeUseCase
{
    public function __construct(
        private Activity_Type_RepositoryInterface $activity_Type_RepositoryInterface
    )
    {}

    public function execute()
    {
        return $this->activity_Type_RepositoryInterface->getAll();
    }
}
