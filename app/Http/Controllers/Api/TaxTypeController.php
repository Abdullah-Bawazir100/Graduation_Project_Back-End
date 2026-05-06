<?php

namespace App\Http\Controllers\Api;

use App\Application\TaxType\DTOs\TaxTypeDTOs;
use App\Application\TaxType\UseCases\CreateTaxTypeUseCase;
use App\Application\TaxType\UseCases\DeleteTaxTypeUseCase;
use App\Application\TaxTYpe\UseCases\ListTaxTypesUseCase;
use App\Application\TaxType\UseCases\ShowTaxTypeUseCase;
use App\Application\TaxType\UseCases\UpdateTaxTypeUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaxType\StoreTaxTypeRequest;
use App\Http\Requests\TaxType\UpdateTaxTypeRequest;
use App\Http\Responses\ApiResponse;

class TaxTypeController extends Controller
{

    public function index(ListTaxTypesUseCase $useCase)
    {
        $taxTypes = $useCase->execute();
        return ApiResponse::created(
            data: $taxTypes,
            message: 'تم جلب أنواع الضرائب بنجاح.'
        );
    }


    public function store(StoreTaxTypeRequest $request , CreateTaxTypeUseCase $useCase)
    {
        $dto = new TaxTypeDTOs(
            name: $request->name,
        );

        $taxType = $useCase->execute($dto);
        return ApiResponse::created(
            data: $taxType,
            message: 'تم إنشاء نوع ضريبة جديد بنجاح.'
        );
    }


    public function show(int $id , ShowTaxTypeUseCase $useCase)
    {
        $taxType = $useCase->execute($id);
        return ApiResponse::ok(
            data: $taxType,
            message: 'تم جلب نوع الضريبة بنجاح.'
        );
    }


    public function update(int $id , UpdateTaxTypeRequest $request , UpdateTaxTypeUseCase $useCase)
    {
        $dto = new TaxTypeDTOs(
            name: $request->name
        );

        $taxType = $useCase->execute($id, $dto);

        return ApiResponse::ok(
            data: $taxType,
            message: 'تم تحديث بيانات نوع الوظيفة بنجاح.'
        );
    }


    public function destroy(int $id , DeleteTaxTypeUseCase $useCase)
    {
        $useCase->execute($id);
        return ApiResponse::ok(
            [],
            message:"تم حذف نوع الضريبة مع ال ID [{$id}] بنجاح."
        );
    }
}
