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
use App\Http\Requests\TaxCollector\StoreTaxCollectorRequest;
use App\Http\Requests\TaxCollector\UpdateTaxCollectorRequest;
use App\Http\Responses\ApiResponse;
use App\Application\User\Services\UploadFileService;
use App\Domain\User\Entities\User as DomainUser;
use App\Domain\Department\Entities\Department;
use App\Domain\User\Enums\UserRole;
use Illuminate\Support\Facades\Auth;

class TaxCollectorController extends Controller
{
    public function __construct(
        private FindByIdUseCase $findTaxCollectorById,
        private UploadFileService $uploadFileService
    ) {}

    public function index(ListTaxCollectorUseCase $useCase): ApiResponse
    {
        $actor = $this->getActor();
        $taxCollectors = $useCase->execute($actor);
        return ApiResponse::ok($taxCollectors, 'تم جلب المأمورين بنجاح.');
    }

    public function store(StoreTaxCollectorRequest $request , CreateTaxCollectorUseCase $useCase): ApiResponse
    {
        $actor = $this->getActor();

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

        $taxCollector = $useCase->execute($actor, $dto);

        return ApiResponse::created($taxCollector, 'تم إنشاء المأمور بنجاح.');
    }

    public function show(int $id): ApiResponse
    {
        $actor = $this->getActor();
        $taxCollector = $this->findTaxCollectorById->execute($actor, $id);
        return ApiResponse::ok($taxCollector, 'تم جلب المأمور بنجاح.');
    }

    public function update(int $id , UpdateTaxCollectorRequest $request, UpdateTaxCollectorUseCase $useCase): ApiResponse
    {
        $actor = $this->getActor();

        $existingUser = $this->findTaxCollectorById->execute($actor, $id);

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

        $taxCollector = $useCase->execute($actor, $id, $dto);

        return ApiResponse::ok($taxCollector, 'تم تحديث بيانات المأمور بنجاح.');
    }

    public function destroy(int $id , DeleteTaxCollectorUseCase $useCase): ApiResponse
    {
        $useCase->execute($id);
        return ApiResponse::ok([], 'تم حذف المأمور مع ال ID [' . $id . '] بنجاح.');
    }

    private function getActor(): DomainUser
    {
        $authUser = Auth::user() ?? throw new \Illuminate\Auth\AuthenticationException();

        $department = new Department(
            id: $authUser->department_id,
            name: $authUser->department?->name ?? ''
        );

        return new DomainUser(
            id: $authUser->id,
            firstName: $authUser->first_name,
            lastName: $authUser->last_name,
            idCard: $authUser->id_card,
            userName: $authUser->user_name,
            phone: $authUser->phone,
            image: $authUser->image,
            password: $authUser->password,
            createdBy: $authUser->created_by,
            department: $department,
            role: $authUser->role,
            mustChangePassword: $authUser->must_change_password,
        );
    }
}
