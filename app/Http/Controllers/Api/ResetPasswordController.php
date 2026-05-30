<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPassword\ResetPasswordRequest;
use App\Application\ResetPassword\DTOs\ResetPasswordDTOs;
use App\Application\ResetPassword\UseCases\RequestResetPasswordUseCase;
use App\Application\ResetPassword\UseCases\VerifyResetCodeUseCase;
use App\Application\ResetPassword\UseCases\ResetPasswordUseCase;
use App\Http\Requests\ResetPassword\StoreResetPasswordRequest;
use App\Http\Requests\ResetPassword\VerifyCodeRequest;

class ResetPasswordController extends Controller
{
    public function request(
        StoreResetPasswordRequest $request,
        RequestResetPasswordUseCase $useCase
    ) {
        $dto = new ResetPasswordDTOs(
            userName: $request->userName
        );

        $result = $useCase->execute($dto);

        return response()->json([
            'status' => true,
            'data' => $result
        ]);
    }

    public function verify(
        VerifyCodeRequest $request,
        VerifyResetCodeUseCase $useCase
    ) {
        $dto = new ResetPasswordDTOs(
            userId: $request->userId,
            code: $request->code
        );

        $result = $useCase->execute($dto);

        return response()->json([
            'status' => true,
            'data' => $result
        ]);
    }

    public function reset(
        ResetPasswordRequest $request,
        ResetPasswordUseCase $useCase
    ) {
        $dto = new ResetPasswordDTOs(
            userId: $request->userId,
            code: $request->code,
            newPassword: $request->newPassword
        );

        $result = $useCase->execute($dto);

        return response()->json([
            'status' => true,
            'data' => $result
        ]);
    }
}
