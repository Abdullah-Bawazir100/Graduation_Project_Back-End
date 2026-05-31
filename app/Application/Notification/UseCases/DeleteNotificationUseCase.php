<?php

namespace App\Application\Notification\UseCases;

use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use DomainException;

class DeleteNotificationUseCase
{
    public function __construct(
        private NotificationRepositoryInterface $notification_repository
    )
    {
    }

    public function execute(int $id)
    {
        $notification = $this->notification_repository->findNotificationById($id);
        if(!$notification)
        {
            throw new DomainException("لا يوجد إشعار مع ال ID [$id].");
        }
        $this->notification_repository->delete($id);
    }
}
