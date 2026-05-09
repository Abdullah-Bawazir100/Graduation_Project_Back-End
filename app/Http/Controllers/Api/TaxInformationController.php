<?php

namespace App\Http\Controllers\Api;

use App\Application\TaxInformation\DTOs\TaxInformationDTOs;
use App\Application\TaxInformation\UseCases\CreateTaxInformationUseCase;
use App\Application\TaxInformation\UseCases\DeleteTaxInformationUseCase;
use App\Application\TaxInformation\UseCases\ListTaxInformationsUseCase;
use App\Application\TaxInformation\UseCases\ShowTaxInformationUseCase;
use App\Application\TaxInformation\UseCases\UpdateTaxInformationUseCase;
use App\Domain\TaxInformation\Repositories\TaxInformationRepositoryInterface;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaxInformation\StoreTaxInformationRequest;
use App\Http\Requests\TaxInformation\UpdateTaxInformationRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;

class TaxInformationController extends Controller
{
    public function __construct(
        private TaxInformationRepositoryInterface $tax_information_repository,
        private TaxPayerRepositoryInterface $tax_payer_repository
    )
    {}

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


    public function update(int $id , UpdateTaxInformationRequest $request , UpdateTaxInformationUseCase $useCase)
    {
        $existingTaxInfo = $this->tax_information_repository->findById($id);
        if(!$existingTaxInfo)
        {
            return ApiResponse::notFound([] , "لا يوجد نوع ضريبة مع ال ID [$id].");
        }

        $dto = new TaxInformationDTOs(
            id: $existingTaxInfo->id,
            taxTypeId: $request->taxTypeId ??  $existingTaxInfo->taxTypeId,
            taxPayerId: $request->taxPayerId ?? $existingTaxInfo->taxPayerId,
            taxAmount: $request->taxAmount ?? $existingTaxInfo->taxAmount,
            lastPayment: $request->lastPayment ?? $existingTaxInfo->lastPayment,
        );

        $updatedTaxInfo = $useCase->execute($id , $dto);
        return ApiResponse::ok(
            $updatedTaxInfo,
            "تم تحديث بيانات معلومات الضريبة مع ال ID [$id] بنجاح."
        );
    }


    public function destroy(int $id , DeleteTaxInformationUseCase $useCase)
    {
        $useCase->execute($id);
        return ApiResponse::ok([] , "تم حذف معلومات الضريبة مع ال ID [$id] بنجاح.");
    }
}
