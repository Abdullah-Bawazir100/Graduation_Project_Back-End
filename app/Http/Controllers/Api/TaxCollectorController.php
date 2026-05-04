<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\TaxCollector\UseCases\CreateTaxCollectorUseCase;
use App\Application\TaxCollector\UseCases\UpdateTaxCollectorUseCase;
use App\Application\TaxCollector\UseCases\DeleteTaxCollectorUseCase;
use App\Application\TaxCollector\UseCases\ShowTaxCollectorUseCase;
use App\Application\TaxCollector\UseCases\FindByIdUseCase;
use App\Application\TaxCollector\UseCases\ListTaxCollectorUseCase;
use App\Application\TaxCollector\DTOs\TaxCollectorDTOs;
use App\Application\TaxCollector\UseCases\MoveTaxCollectorsToAnotherDeptUseCase;
use App\Http\Requests\TaxCollector\StoreTaxCollectorRequest;
use App\Http\Requests\TaxCollector\UpdateTaxCollectorRequest;
use App\Http\Responses\ApiResponse;
use App\Application\User\Services\UploadFileService;
use App\Http\Requests\TaxCollector\MoveTaxCollectorsRequest;

class TaxCollectorController extends Controller
{
    public function __construct(
        private FindByIdUseCase $findTaxCollectorById,
        private UploadFileService $uploadFileService
    ) {}

    public function index(ListTaxCollectorUseCase $useCase): ApiResponse
    {
        $taxCollectors = $useCase->execute();
        return ApiResponse::ok($taxCollectors, 'تم جلب المأمورين بنجاح.');
    }

    public function store(StoreTaxCollectorRequest $request , CreateTaxCollectorUseCase $useCase): ApiResponse
    {
        $idCardUrl = null;
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

        return ApiResponse::created($taxCollector, 'تم إنشاء المأمور بنجاح.');
    }

    public function show(int $id): ApiResponse
    {
        $taxCollector = $this->findTaxCollectorById->execute($id);

        if (!$taxCollector) {
            return ApiResponse::notFound([] , 'المأمور مع ال ID [' . $id . '] غير موجود.');
        }

        return ApiResponse::ok($taxCollector, 'تم جلب المأمور بنجاح.');
    }

    public function update(int $id , UpdateTaxCollectorRequest $request, UpdateTaxCollectorUseCase $useCase): ApiResponse
    {
        $existingUser = $this->findTaxCollectorById->execute($id);
        if  (!$existingUser) {
            return ApiResponse::notFound([] , 'المأمور مع ال ID [' . $id . '] غير موجود.');
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

        return ApiResponse::ok($taxCollector, 'تم تحديث بيانات المأمور بنجاح.');
    }

    public function destroy(int $id , DeleteTaxCollectorUseCase $useCase): ApiResponse
    {
        $useCase->execute($id);
        return ApiResponse::ok([], 'تم حذف المأمور مع ال ID [' . $id . '] بنجاح.');
    }

    public function moveTaxCollectorsToAnotherDepartment(int $id,
    MoveTaxCollectorsRequest $request , MoveTaxCollectorsToAnotherDeptUseCase $useCase): ApiResponse
    {
        $newDepartmentId = $request->new_departmentId;
        $useCase->execute($id , $newDepartmentId);
        return ApiResponse::ok([], "تم نقل المأمورين من القسم [{$id}] إلى القسم [{ $newDepartmentId}] بنجاح.");
    }

}
