<?php

namespace App\Application\Notification\UseCases;

use App\Domain\Notification\Repositories\NotificationRepositoryInterface;

class ListNotificationsUseCase
{
    public function __construct(
        private NotificationRepositoryInterface $notification_repository
    )
    {
    }

    public function execute()
    {
        $notifications = $this->notification_repository->getAll();
        return $notifications;
    }
}
