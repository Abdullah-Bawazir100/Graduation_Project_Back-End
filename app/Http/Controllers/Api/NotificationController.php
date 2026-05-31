<?php

namespace App\Http\Controllers\Api;

use App\Application\Notification\DTOs\NotificationDTOs;
use App\Application\Notification\UseCases\CreateNotificationUseCase;
use App\Application\Notification\UseCases\FindNotificationByIdUseCase;
use App\Application\Notification\UseCases\ListNotificationsUseCase;
use App\Domain\Notification\Enums\enNotificationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\StoreNotificationRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

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


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
}
