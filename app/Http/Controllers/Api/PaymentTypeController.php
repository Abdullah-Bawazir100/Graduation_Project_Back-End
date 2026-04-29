<?php

namespace App\Http\Controllers\Api;

use App\Application\PaymentType\DTOs\PaymentTypeDTOs;
use App\Application\PaymentType\UseCases\CreatePaymentTypeUseCase;
use App\Application\PaymentType\UseCases\DeletePaymentTypeUseCase;
use App\Application\PaymentType\UseCases\ListPaymentTypesUseCase;
use App\Application\PaymentType\UseCases\ShowPaymentTypeUseCase;
use App\Application\PaymentType\UseCases\UpdatePaymentTypeUseCase;
use App\Application\PaymentType\UseCases\CountPaymentsTypesUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentType\StorePaymentTypeRequest;
use App\Http\Requests\PaymentType\UpdatePaymentTypeRequest;
use App\Http\Responses\ApiResponse;

class PaymentTypeController extends Controller
{

    public function index(ListPaymentTypesUseCase $useCase)
    {
        $paymentTypes = $useCase->execute();
        return ApiResponse::ok(
            data: $paymentTypes,
            message: 'تم جلب أنواع الدفع بنجاح.'
        );
    }


    public function store(StorePaymentTypeRequest $request , CreatePaymentTypeUseCase $useCase)
    {
        $dto = new PaymentTypeDTOs(
            name: $request->name,
            note: $request->note
        );

        $paymentType = $useCase->execute($dto);

        return ApiResponse::created(
            data: $paymentType,
            message: 'تم إنشاء نوع دفع بنجاح.'
        );
    }


    public function show(int $id , ShowPaymentTypeUseCase $useCase)
    {
        $paymentType = $useCase->execute($id);
        return ApiResponse::ok(
            data: $paymentType,
            message: "تم جلب نوع الدفع مع ال ID [{$id}] بنجاح."
        );
    }


    public function update(int $id , UpdatePaymentTypeRequest $request , UpdatePaymentTypeUseCase $useCase)
    {
        $dto = new PaymentTypeDTOs(
            name: $request->validated('name'),
            note: $request->validated('note')
        );

        $paymentType = $useCase->execute($id , $dto);

        return ApiResponse::ok(
            data: $paymentType,
            message: "تم تحديث بيانات نوع الدفع مع ال ID [{$id}] بنجاح."
        );
    }


    public function destroy(int $id , DeletePaymentTypeUseCase $useCase)
    {
        $useCase->execute($id);
        return ApiResponse::ok(
            data: null,
            message: "تم حذف نوع الدفع مع ال ID [{$id}] بنجاح."
        );
    }
}
