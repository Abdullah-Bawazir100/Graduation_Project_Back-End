<?php

namespace App\Application\Notification\UseCases;

use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\Notification\Enums\enNotificationType;
use Illuminate\Support\Facades\Auth;

class ListNotificationsUseCase
{
    public function __construct(
        private NotificationRepositoryInterface $notification_repository
    )
    {
    }

    public function execute()
    {
        $user = Auth::user();
        $notifications = $this->notification_repository->getAll();

        $notificationsCollection = collect($notifications);

        if ($user && $user->role !== UserRole::Admin) {
            $notificationsCollection = $notificationsCollection->filter(function ($notification) use ($user) {
                return $notification->sendBy->department->id === $user->department_id;
            })->values();
        }

        return [
            'notifications' => $notificationsCollection->all(),
        ];
    }
}
