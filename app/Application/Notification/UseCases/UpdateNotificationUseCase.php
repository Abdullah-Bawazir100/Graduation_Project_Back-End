<?php

namespace App\Application\Notification\UseCases;

use App\Application\Notification\DTOs\NotificationDTOs;
use App\Domain\Notification\Entities\Notification;
use App\Domain\Notification\Enums\enNotificationType;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class UpdateNotificationUseCase
{
    public function __construct(
        private NotificationRepositoryInterface $notification_repository,
        private UserRepositoryInterface $user_repository
    )
    {
    }

    public function execute(int $id , NotificationDTOs $notificationDTOs)
    {
        $existingNotification = $this->notification_repository->findNotificationById($id);
        if(!$existingNotification)
        {
            throw new DomainException("لا يوجد إشعار مع ال ID [$id].");
        }

        $user = $this->user_repository->findById($notificationDTOs->sendBy);

        $receiverPhone = $notificationDTOs->receiverPhone;
        if($notificationDTOs->notificationType !== enNotificationType::Special)
        {
            $receiverPhone = $notificationDTOs->notificationType->value;
        }

        $notification = new Notification(
            id: $existingNotification->id,
            title: $notificationDTOs->title,
            description: $notificationDTOs->description,
            notificationType: $notificationDTOs->notificationType,
            receiverPhone: $receiverPhone,
            sendBy: $user,
        );

        $updatedNotification = $this->notification_repository->update($id , $notification);
        return $updatedNotification;
    }
}
