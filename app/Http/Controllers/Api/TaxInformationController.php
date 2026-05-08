<?php

namespace App\Http\Controllers\Api;

use App\Application\TaxInformation\DTOs\TaxInformationDTOs;
use App\Application\TaxInformation\UseCases\CreateTaxInformationUseCase;
use App\Application\TaxInformation\UseCases\DeleteTaxInformationUseCase;
use App\Application\TaxInformation\UseCases\ListTaxInformationsUseCase;
use App\Application\TaxInformation\UseCases\ShowTaxInformationUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaxInformation\StoreTaxInformationRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;

class TaxInformationController extends Controller
{

    public function index(ListTaxInformationsUseCase $useCase)
    {
        $taxInformations = $useCase->execute();
        return ApiResponse::ok($taxInformations , "تم جلب جميع معلومات الضرائب بنجاح.");
    }


    public function store(StoreTaxInformationRequest $request , CreateTaxInformationUseCase $useCase)
    {
        $dto = new TaxInformationDTOs(
            id: null,
            taxAmount: $request->taxAmount,
            lastPayment: $request->lastPayment,
            taxTypeId:  $request->taxTypeId,
            taxPayerId: $request->taxPayerId,
        );
        $result = $useCase->execute($dto);
        return ApiResponse::created($result , "تم انشاء معلومات الضريبة بنجاح.");
    }


    public function show(int $id , ShowTaxInformationUseCase $useCase)
    {
        $taxInfo = $useCase->execute($id);
        return ApiResponse::ok($taxInfo , "تم جلب معلومات الضريبة مع ال ID [$id] بنجاح.");
    }


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(int $id , DeleteTaxInformationUseCase $useCase)
    {
        $useCase->execute($id);
        return ApiResponse::ok([] , "تم حذف معلومات الضريبة مع ال ID [$id] بنجاح.");
    }
}
