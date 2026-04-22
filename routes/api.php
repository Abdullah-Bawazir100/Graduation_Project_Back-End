<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\ActivityTypeController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentTypeController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ManagerMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('login' , [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {

    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('complete-profile', [AuthController::class, 'completeProfile']);
    Route::post('logout' , [AuthController::class , 'logout']);
    Route::get('get_user/{id}' , [UserController::class , 'show']);

});

Route::middleware(['auth:sanctum' , AdminMiddleware::class])->group(function () {

    Route::post('create-user' , [AuthController::class, 'createUser']);
    Route::apiResource('app_users', UserController::class);
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('activity_types' , ActivityTypeController::class);
    Route::get('activity-log' , [ActivityLogController::class , 'index']);
    Route::apiResource('payment_types' , PaymentTypeController::class);
    Route::apiResource('regions' , RegionController::class);

});

Route::middleware(['auth:sanctum' , ManagerMiddleware::class])->group(function() {

    Route::apiResource('manager-departments' , DepartmentController::class);
    Route::apiResource('manager-activity_types' , ActivityTypeController::class);

});






