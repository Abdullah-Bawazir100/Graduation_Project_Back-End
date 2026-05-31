<?php

namespace App\Domain\Notification\Repositories;

use App\Domain\Notification\Entities\Notification;

interface NotificationRepositoryInterface
{
    public function create(Notification $notification): Notification;
    public function getAll();
    public function findNotificationById(int $id);
}
