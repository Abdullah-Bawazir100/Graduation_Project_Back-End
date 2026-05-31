<?php

namespace App\Http\Controllers\Api;

use App\Application\Notification\DTOs\NotificationDTOs;
use App\Application\Notification\UseCases\CreateNotificationUseCase;
use App\Application\Notification\UseCases\DeleteNotificationUseCase;
use App\Application\Notification\UseCases\FindNotificationByIdUseCase;
use App\Application\Notification\UseCases\ListNotificationsUseCase;
use App\Application\Notification\UseCases\UpdateNotificationUseCase;
use App\Domain\Notification\Enums\enNotificationType;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\StoreNotificationRequest;
use App\Http\Requests\Notification\UpdateNotificationRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationRepositoryInterface $notification_repository
    )
    {
    }

    public function index(ListNotificationsUseCase $useCase)
    {
        $notifications = $useCase->execute();
        return ApiResponse::ok(
            data: $notifications,
            message: 'تم جلب الإشعارات بنجاح.'
        );
    }


    public function store(StoreNotificationRequest $request , CreateNotificationUseCase $useCase)
    {
        $actor = Auth::user();
        $dto = new NotificationDTOs(
            title: $request->title,
            description: $request->description,
            notificationType: enNotificationType::from($request->notificationType),
            receiverPhone:  $request->receiverPhone,
            sendBy: null
        );

        $createdNotification = $useCase->execute($dto , $actor->id);

        return ApiResponse::created(
            data: $createdNotification,
            message: 'تم انشاء الاشعار بنجاح'
        );

    }

    public function show(int $id , FindNotificationByIdUseCase $useCase)
    {
        $notification = $useCase->execute($id);
        return ApiResponse::ok(
            data: $notification,
            message: "تم جلب الإشعار مع ال ID [$id] بنجاح."
        );
    }


    public function update(int $id , UpdateNotificationRequest $request,
    UpdateNotificationUseCase $useCase)
    {
        $existingNotification = $this->notification_repository->findNotificationById($id);
        if(!$existingNotification)
        {
            return ApiResponse::notFound(
                message: "لا يوجد إشعار مع ال ID [$id]."
            );
        }

        $notificationTypeInput = $request->input('notificationType');
        $notificationType = $notificationTypeInput !== null
            ? enNotificationType::from($notificationTypeInput)
            : $existingNotification->notificationType;
            
        $dto = new NotificationDTOs(
            title: $request->title ?? $existingNotification->title,
            description: $request->description ?? $existingNotification->description,
            notificationType: $notificationType,
            receiverPhone: $request->receiverPhone ?? $existingNotification->receiverPhone,
            sendBy: $existingNotification->sendBy->id,
        );

        $updatedNotification = $useCase->execute($id , $dto);
        return ApiResponse::ok(
            data: $updatedNotification,
            message: "تم تحديث الإشعار مع ال ID [$id] بنجاح."
        );
    }


    public function destroy(int $id , DeleteNotificationUseCase $useCase)
    {
        $useCase->execute($id);
        return ApiResponse::ok(
            message: "تم حذف الإشعار مع ال ID [$id] بنجاح."
        );
    }
}
