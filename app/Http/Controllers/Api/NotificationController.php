<?php

namespace App\Http\Controllers\Api;

use App\Application\Notification\DTOs\NotificationDTOs;
use App\Application\Notification\UseCases\CreateNotificationUseCase;
use App\Domain\Notification\Enums\enNotificationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\StoreNotificationRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    public function index()
    {
        //
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
