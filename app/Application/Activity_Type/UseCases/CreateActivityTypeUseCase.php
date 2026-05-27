<?php

namespace App\Application\Activity_Type\UseCases;

use App\Application\Activity_Type\DTOs\ActivityTypeDTOs;
use App\Domain\Activity_Type\Entities\Activity_Type;
use App\Domain\Activity_Type\Repositories\Activity_Type_RepositoryInterface;

class CreateActivityTypeUseCase
{
    public function __construct(
        private Activity_Type_RepositoryInterface $activity_Type_RepositoryInterface
    )
    {}

    public function execute(ActivityTypeDTOs $activityTypeDTOs)
    {
        $name = trim($activityTypeDTOs->name);

        return $this->activity_Type_RepositoryInterface->create(

            new Activity_Type(null , $name)

        );
    }
}
