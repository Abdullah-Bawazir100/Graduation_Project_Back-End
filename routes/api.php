<?php

use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


 Route::post('signup' , [AuthController::class, 'signUp']);
Route::post('login' , [AuthController::class, 'login']);

// Reset Password
Route::post('reset-password', [AuthController::class, 'resetPassword']);

// Complete Profile After Reset Password
Route::post('complete-profile', [AuthController::class, 'completeProfile']);

Route::middleware('auth:sanctum')->group(function () {

    // Create Users By [Admin , Manager]
    Route::post('create-user' , [AuthController::class, 'createUser']);

    Route::apiResource('app_users', controller: UserController::class);
});

Route::apiResource('departments', DepartmentController::class);


