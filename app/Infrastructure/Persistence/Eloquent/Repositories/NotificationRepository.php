<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Department\Entities\Department;
use App\Domain\Notification\Entities\Notification;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Infrastructure\Persistence\Eloquent\Models\NotificationModel;
use League\CommonMark\Extension\DescriptionList\Node\Description;
use Override;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function create(Notification $notification): Notification
    {
        $notificationModel = NotificationModel::create([
            'title' => $notification->title,
            'description' => $notification->description,
            'notification_type' => $notification->notificationType->value,
            'receiver_phone' => $notification->receiverPhone,
            'send_by' => $notification->sendBy->id,
        ]);
        $notificationModel->load('user');

        return $this->mapToDomain($notificationModel);
    }

    public function update(int $id , Notification $notification): ?Notification
    {
        $notificationModel = NotificationModel::with('user')->find($id);
        if(!$notificationModel)
            return null;

        $notificationModel->update([
            'title' => $notification->title,
            'description' => $notification->description,
            'notification_type' => $notification->notificationType->value,
            'receiver_phone' => $notification->receiverPhone,
            'send_by' => $notification->sendBy->id
        ]);

        $notificationModel->refresh();
        return $this->mapToDomain($notificationModel);
    }

    public function getAll()
    {
        $notifications = NotificationModel::with('user')->get();
        return $notifications->map(fn(NotificationModel $model) => $this->mapToDomain($model))->toArray();
    }

    public function findNotificationById(int $id)
    {
        $notification = NotificationModel::with('user')->find($id);

        if(!$notification)
            return null;

        return $this->mapToDomain($notification);
    }

    public function delete(int $id)
    {
        NotificationModel::findOrFail($id)->delete();
    }

    private function mapToDomain(NotificationModel $model): Notification
    {

        $user = new User(
            id: $model->user->id,
            firstName: $model->user->first_name,
            lastName: $model->user->last_name,
            idCard: $model->user->id_card ?? '',
            userName: $model->user->user_name,
            phone: $model->user->phone,
            image: $model->user->image ?? '',
            password: $model->user->password,
            createdBy: $model->user->created_by,
            department: new Department(
                id: $model->user->department?->id ?? 0,
                name: $model->user->department?->name ?? ''
            ),
            role: $model->user->role,
            mustChangePassword: $model->user->must_change_password ?? false
        );

        return new Notification(
            id: $model->id,
            title: $model->title,
            description: $model->description,
            notificationType: $model->notification_type,
            receiverPhone: $model->receiver_phone,
            sendBy: $user
        );
    }
}
