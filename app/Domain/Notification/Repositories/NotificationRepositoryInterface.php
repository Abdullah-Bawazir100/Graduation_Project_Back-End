<?php

namespace App\Domain\Notification\Repositories;

use App\Domain\Notification\Entities\Notification;
use App\Domain\Notification\Enums\enNotificationType;

interface NotificationRepositoryInterface
{
    public function create(Notification $notification): Notification;
    public function update(int $id , Notification $notification): ?Notification;
    public function getAll();
    public function findNotificationById(int $id);
    public function delete(int $id);
    public function sendSmsNotification(Notification $notification): void;
}
