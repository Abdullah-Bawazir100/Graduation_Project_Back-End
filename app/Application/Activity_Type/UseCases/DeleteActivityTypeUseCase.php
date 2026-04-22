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
            throw new \DomainException("نوع النشاط مع ال ID [{$id}] غير موجود.");
        }

        $this->activity_type_repository_interface->delete($id);
    }
}
