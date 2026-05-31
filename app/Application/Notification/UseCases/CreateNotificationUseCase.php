<?php

namespace App\Application\Notification\UseCases;

use App\Application\Notification\DTOs\NotificationDTOs;
use App\Domain\Notification\Entities\Notification;
use App\Domain\Notification\Enums\enNotificationType;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use DomainException;

class CreateNotificationUseCase
{
    public function __construct(
        private NotificationRepositoryInterface $notification_repository,
        private UserRepositoryInterface $user_repository
    )
    {
    }

    public function execute(NotificationDTOs $notificationDTOs , int $authenticatedUserId)
    {
        $creator =$this->user_repository->findById($authenticatedUserId);
        if(!$creator)
        {
            throw new DomainException("لا يوجد مستخدم مع ال ID [$authenticatedUserId].");
        }

        $receiverPhone = $notificationDTOs->receiverPhone;
        if($notificationDTOs->notificationType !== enNotificationType::Special)
        {
            $receiverPhone = $notificationDTOs->notificationType->value;
        }

        $notification = new Notification(
            id: null,
            title: $notificationDTOs->title,
            description: $notificationDTOs->description,
            notificationType: $notificationDTOs->notificationType,
            receiverPhone: $receiverPhone,
            sendBy: $creator
        );

        $createdNotification = $this->notification_repository->create($notification);
        return $createdNotification;
    }
}
