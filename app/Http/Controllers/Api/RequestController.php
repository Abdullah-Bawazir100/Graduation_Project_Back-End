<?php

namespace App\Http\Controllers\Api;

use App\Application\File\DTOs\FileDTOs;
use App\Application\Request\UseCases\CreateRequestUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaxPayerRequest\StoreRequestOfTaxPayerRequest;
use DomainException;
use Illuminate\Http\Request;
use App\Application\Request\DTOs\TaxPayerRequestDTOs;
use App\Application\Request\UseCases\AcceptRequestUseCase;
use App\Application\Request\UseCases\ArchiveRequestUseCase;
use App\Application\Request\UseCases\DeleteRequestUseCase;
use App\Application\Request\UseCases\FindRequestByIdUseCase;
use App\Application\Request\UseCases\ListArchivedRequestsUseCase;
use App\Application\Request\UseCases\ListConfirmedRequestsUseCase;
use App\Application\Request\UseCases\ListPendingRequestsUseCase;
use App\Application\Request\UseCases\ListRejectedRequestsUseCase;
use App\Application\Request\UseCases\ListRequestsUseCase;
use App\Application\Request\UseCases\RejectRequestUseCase;
use App\Domain\TaxPayer\Enums\enFileType;
use App\Domain\Request\Enums\enRequestStatus;
use App\Http\Responses\ApiResponse;
use App\Application\User\Services\UploadFileService;
use App\Domain\User\Enums\UserRole;
use App\Http\Requests\File\StoreFileRequest;
use App\Http\Requests\TaxPayerRequest\AcceptRequestOfTaxPayerRequest;
use App\Http\Requests\TaxPayerRequest\ArchiveRequestOfTaxPayerRequest;
use App\Http\Requests\TaxPayerRequest\RejectRequestOfTaxPayerRequest;
use Exception;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function __construct(
        private UploadFileService $uploadFileService
    ) {}

    public function index(ListRequestsUseCase $useCase)
    {
        $allRequests = $useCase->execute(Auth::id());
        return ApiResponse::ok(
            data: $allRequests,
            message: "تم جلب جميع الطلبات بنجاح."
        );
    }

    public function getPendingRequests(ListPendingRequestsUseCase $useCase)
    {
        $pendingRequests = $useCase->execute(Auth::id());
        return ApiResponse::ok(
            data: $pendingRequests,
            message: "تم جلب الطلبات قيد الإنتظار بنجاح."
        );
    }

    public function getConfirmedRequests(ListConfirmedRequestsUseCase $useCase)
    {
        $confirmedRequests = $useCase->execute(Auth::id());
        return ApiResponse::ok(
            data: $confirmedRequests,
            message: "تم جلب الطلبات المؤكدة بنجاح."
        );
    }

    public function getArchivedRequests(ListArchivedRequestsUseCase $useCase)
    {
        $confirmedRequests = $useCase->execute(Auth::id());
        return ApiResponse::ok(
            data: $confirmedRequests,
            message: "تم جلب الطلبات المرحلة بنجاح."
        );
    }

    public function getRejectedRequests(ListRejectedRequestsUseCase $useCase)
    {
        $confirmedRequests = $useCase->execute(Auth::id());
        return ApiResponse::ok(
            data: $confirmedRequests,
            message: "تم جلب الطلبات المرفوضة بنجاح."
        );
    }

    public function store(StoreRequestOfTaxPayerRequest $request, CreateRequestUseCase $useCase): ApiResponse
    {
        try {
            // جلب المستخدم الحالي
            $authUser = Auth::user();

            if (!$authUser) {
                return ApiResponse::unauthorized();
            }

            if($authUser->role !== UserRole::Tax_Payer)
            {
                return ApiResponse::forbidden([], 'عفواً، هذا الإجراء مسموح فقط للمكلفين.');
            }

            $commercialRecordUrl = $request->hasFile('commercialRecord')
                ? $this->uploadFileService->uploadFile($request->file('commercialRecord'), 'commercial-records')
                : null;

            $activityLicenseUrl = $request->hasFile('activityLicense')
                ? $this->uploadFileService->uploadFile($request->file('activityLicense'), 'activity-licenses')
                : null;

            $tradePictUrl = $request->hasFile('tradePict')
                ? $this->uploadFileService->uploadFile($request->file('tradePict'), 'trade-picts')
                : null;

            $insuranceCardUrl = $request->hasFile('insuranceCard')
                ? $this->uploadFileService->uploadFile($request->file('insuranceCard'), 'insurance-cards')
                : null;

            $propertyDocPictUrl = $request->hasFile('propertyDocPict')
                ? $this->uploadFileService->uploadFile($request->file('propertyDocPict'), 'property-docs-picts')
                : null;

            $articlesOfIncorporationUrl = $request->hasFile('articlesOfIncorporation')
                ? $this->uploadFileService->uploadFile($request->file('articlesOfIncorporation'), 'articles-of-incorporation')
                : null;

            $govemorLicenseUrl = $request->hasFile('govemorLicense')
                ? $this->uploadFileService->uploadFile($request->file('govemorLicense'), 'governor-licenses')
                : null;

            $partnersIDCardsUrl = $request->hasFile('partnersIDCards')
                ? $this->uploadFileService->uploadFile($request->file('partnersIDCards'), 'partners-id-cards')
                : null;

            $byLawsCopyUrl = $request->hasFile('byLawsCopy')
                ? $this->uploadFileService->uploadFile($request->file('byLawsCopy'), 'by-laws-copies')
                : null;
            $taxPayerDTO = new TaxPayerRequestDTOs(
                userId: $authUser->id,
                tradeName: $request->tradeName,
                commercialRecord: $commercialRecordUrl,
                activityLicense: $activityLicenseUrl,
                tradePict: $tradePictUrl,
                insuranceCard: $insuranceCardUrl,
                propertyDocPict: $propertyDocPictUrl,
                fileType: enFileType::from($request->fileType),
                articlesOfIncorporation: $articlesOfIncorporationUrl,
                govemorLicense: $govemorLicenseUrl,
                partnersIDCards: $partnersIDCardsUrl,
                byLawsCopy: $byLawsCopyUrl,
                requestStatus: enRequestStatus::Pending,
                note: $request->note,
                source: 'Requests'
            );

            $result = $useCase->execute($taxPayerDTO, Auth::id());

            return ApiResponse::created($result, 'تم إرسال طلب فتح الملف بنجاح.');

        } catch (Exception $e) {
            return ApiResponse::serverError([], $e->getMessage());
        }
    }

    public function acceptRequest(AcceptRequestOfTaxPayerRequest $request ,
    AcceptRequestUseCase $useCase)
    {
        try {
            $requestId = $request->requestId;

            $result = $useCase->execute($requestId, Auth::id());
            return ApiResponse::ok(
                data: $result,
                message: "تم قبول الطلب مع ال ID [$requestId] بنجاح."
            );

        } catch (DomainException $e) {
            return ApiResponse::serverError([], $e->getMessage());
        }
    }

    public function rejectRequest(RejectRequestOfTaxPayerRequest $request ,
    RejectRequestUseCase $useCase)
    {
        try {
            $requestId = $request->requestId;
            $note = $request->note ?? null;

            $result = $useCase->execute($requestId , $note, Auth::id());
            return ApiResponse::ok(
                data: $result,
                message: "تم رفض الطلب مع ال ID [$requestId]."
            );

        } catch (DomainException $e) {
            return ApiResponse::serverError([], $e->getMessage());
        }
    }

    public function show(int $id , FindRequestByIdUseCase $useCase)
    {
        $request = $useCase->execute($id, Auth::id());
        return ApiResponse::ok(
            data: $request,
            message: "تم جلب الطلب مع ال ID [$id] بنجاح."
        );
    }

    public function storeArchivedRequestToFilesTable(ArchiveRequestOfTaxPayerRequest $rejectedRequest
    , StoreFileRequest $request ,
    ArchiveRequestUseCase $useCase)
    {
        try {
            $requestId = $rejectedRequest->requestId;
            $authenticatedUser = Auth::user();

            if (!$authenticatedUser) {
                return response()->json([
                    'error' => 'المستخدم غير مسجل الدخول'
                ], 401);
            }

            $dto = new FileDTOs(
                taxNumber: $request->taxNumber,
                inventoryNumber: $request->inventoryNumber,
                activityStartDate: $request->activityStartDate,
                docsCount: $request->docsCount,
                note: $request->note,
                taxPayerId: $request->taxPayerId,
                departmentId:  $request->departmentId,
                fileStatusId:  $request->fileStatusId,
                activityTypeId: $request->activityTypeId,
                paymentTypeId: $request->paymentTypeId,
                regionId: $request->regionId,
                districtId:  $request->districtId
            );
            $result = $useCase->execute($requestId , $dto , $authenticatedUser->id);
            return ApiResponse::ok(
                data: $result,
                message: "تم ترحيل الطلب مع ال ID [$requestId] بنجاح."
            );

        } catch (DomainException $e) {
            return ApiResponse::serverError([], $e->getMessage());
        }
    }


    // public function update(Request $request, string $id)
    // {
    //     //
    // }


    public function destroy(int $requestId , DeleteRequestUseCase $useCase)
    {
        $useCase->execute($requestId, Auth::id());
        return ApiResponse::ok(
            data: [],
            message: "تم حذف الطلب مع ال ID [$requestId] بنجاح."
        );
    }
}
