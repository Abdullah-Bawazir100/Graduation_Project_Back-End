<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\TaxCollector\UseCases\CreateTaxCollectorUseCase;
use App\Application\TaxCollector\UseCases\UpdateTaxCollectorUseCase;
use App\Application\TaxCollector\UseCases\DeleteTaxCollectorUseCase;
use App\Application\TaxCollector\UseCases\ShowTaxCollectorUseCase;
use App\Application\TaxCollector\UseCases\FindByIdUseCase;
use App\Application\TaxCollector\UseCases\ListTaxCollectorUseCase;
use App\Application\TaxCollector\UseCases\FindByNameUseCase;
use App\Application\TaxCollector\DTOs\TaxCollectorDTOs;
use App\Http\Requests\TaxCollector\StoreTaxCollectorRequest;
use App\Http\Requests\TaxCollector\UpdateTaxCollectorRequest;
use App\Http\Responses\ApiResponse;
use App\Application\User\Services\UploadFileService;

class TaxCollectorController extends Controller
{
    public function __construct(
        private DeleteTaxCollectorUseCase $deleteTaxCollector,
        private ShowTaxCollectorUseCase $showTaxCollectors,
        private FindByIdUseCase $findTaxCollectorById,
        private FindByNameUseCase $findTaxCollectorByName,
        private UploadFileService $uploadFileService
    ) {}

    public function index(ListTaxCollectorUseCase $useCase): ApiResponse
    {
        $taxCollectors = $useCase->execute();
        return ApiResponse::ok($taxCollectors, 'تم جلب جامعي الضرائب بنجاح.');
    }

    public function store(StoreTaxCollectorRequest $request , CreateTaxCollectorUseCase $useCase): ApiResponse
    {
        if ($request->hasFile('idCard')) {
            $idCardUrl = $this->uploadFileService->uploadFile($request->file('idCard') , 'id-cards');
        }

        $dto = new TaxCollectorDTOs(
            id: null,
            fullName: $request->fullName,
            idCard: $idCardUrl,
            phone: $request->phone,
            jobTypeId: $request->jobTypeId,
            deptID: $request->deptID
        );

        $taxCollector = $useCase->execute($dto);

        return ApiResponse::created($taxCollector, 'تم إنشاء جامع الضرائب بنجاح.');
    }

    public function show(int $id): ApiResponse
    {
        $taxCollector = $this->findTaxCollectorById->execute($id);

        if (!$taxCollector) {
            return ApiResponse::notFound([] , 'جامع الضرائب مع ال ID [' . $id . '] غير موجود.');
        }

        return ApiResponse::ok($taxCollector, 'تم جلب جامع الضرائب بنجاح.');
    }

    public function update(int $id , UpdateTaxCollectorRequest $request, UpdateTaxCollectorUseCase $useCase): ApiResponse
    {
        $existingUser = $this->findTaxCollectorById->execute($id);
        if  (!$existingUser) {
            return ApiResponse::notFound([] , 'جامع الضرائب مع ال ID [' . $id . '] غير موجود.');
        }

        $idCardUrl = $existingUser->idCard;
        if($request->hasFile('idCard')){
            $idCardUrl = $this->uploadFileService->uploadFile($request->file('idCard') , 'id-cards');
        }

        $dto = new TaxCollectorDTOs(
            id: $id,
            fullName: $request->fullName ?? $existingUser->fullName,
            idCard: $idCardUrl ?? $existingUser->idCard,
            phone: $request->phone ?? $existingUser->phone,
            jobTypeId: $request->jobTypeId ?? $existingUser->jobTypeId,
            deptID: $request->deptID ?? $existingUser->deptID,
        );

        $taxCollector = $useCase->execute($id , $dto);

        return ApiResponse::ok($taxCollector, 'تم تحديث بيانات جامع الضرائب بنجاح.');
    }

    public function destroy(int $id , DeleteTaxCollectorUseCase $useCase): ApiResponse
    {
        $useCase->execute($id);
        return ApiResponse::ok([], 'تم حذف جامع الضرائب مع ال ID [' . $id . '] بنجاح.');
    }
}
