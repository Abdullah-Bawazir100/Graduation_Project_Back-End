<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\ActivityTypeController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ManagerMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('login' , [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {

    Route::post('signup' , [AuthController::class, 'signUp']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('complete-profile', [AuthController::class, 'completeProfile']);

});

Route::middleware(['auth:sanctum' , AdminMiddleware::class])->group(function () {

    Route::post('create-user' , [AuthController::class, 'createUser']);
    Route::apiResource('app_users', UserController::class);
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('activity_types' , ActivityTypeController::class);
    Route::get('activity-log' , [ActivityLogController::class , 'index']);

});

Route::middleware(['auth:sanctum' , ManagerMiddleware::class])->group(function() {
    
    Route::apiResource('manager-departments' , DepartmentController::class);
    Route::apiResource('manager-activity_types' , ActivityTypeController::class);

});






