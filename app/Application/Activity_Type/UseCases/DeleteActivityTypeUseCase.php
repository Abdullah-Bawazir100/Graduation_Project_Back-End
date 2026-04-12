<?php

namespace App\Application\Activity_Type\UseCases;

use App\Domain\Activity_Type\Repositories\Activity_Type_RepositoryInterface;

class DeleteActivityTypeUseCase
{
    public function __construct(
        private Activity_Type_RepositoryInterface $activity_type_repository_interface
    )
    {}

    public function execute(int $id)
    {
        $activityType = $this->activity_type_repository_interface->findById($id);

        if (!$activityType) {
            throw new \DomainException("Activity Type with ID [$id] not found.");
        }

        $this->activity_type_repository_interface->delete($id);
    }
}
