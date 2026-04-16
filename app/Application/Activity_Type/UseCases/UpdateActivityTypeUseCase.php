<?php

namespace App\Application\Activity_Type\UseCases;

use App\Application\Activity_Type\DTOs\ActivityTypeDTOs;
use App\Domain\Activity_Type\Entities\Activity_Type;
use App\Domain\Activity_Type\Repositories\Activity_Type_RepositoryInterface;

class UpdateActivityTypeUseCase
{
    public function __construct(
        private Activity_Type_RepositoryInterface $activity_Type_RepositoryInterface
    )
    {}

    public function execute(int $id , ActivityTypeDTOs $activityTypeDTOs)
    {
        $activityType = $this->activity_Type_RepositoryInterface->findById($id);
        if(!$activityType)
        {
            throw new \Exception("Activity Type with ID [$id] not found.");
        }

        $name = $activityTypeDTOs->name ?? $activityType->name;

        // if(
        //     $name !== $activityType->name
        //     && $this->activity_Type_RepositoryInterface->existsByName($name)
        // ) {
        //     throw new \DomainException("Activity Type with name '{$name}' already exists.");
        // }

        return $this->activity_Type_RepositoryInterface->update(
            new Activity_Type($id , $name)
        );
    }
}
