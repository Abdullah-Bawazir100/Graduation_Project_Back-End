<?php

namespace App\Application\Notification\UseCases;

use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use DomainException;

class FindNotificationByIdUseCase
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
        return $notification;
    }
}
