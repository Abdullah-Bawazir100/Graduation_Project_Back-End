<?php

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
    Route::apiResource('app_users', controller: UserController::class);
    Route::apiResource('departments', DepartmentController::class);

});

Route::middleware([ManagerMiddleware::class])->group(function () {

    Route::apiResource('departments' , DepartmentController::class)->except(['destroy']);

});




