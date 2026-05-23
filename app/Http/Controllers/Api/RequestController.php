<?php

namespace App\Http\Controllers\Api;

use App\Application\Request\UseCases\CreateRequestUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaxPayerRequest\StoreRequestOfTaxPayerRequest;
use DomainException;
use Illuminate\Http\Request;
use App\Application\Request\DTOs\TaxPayerRequestDTOs;
use App\Application\Request\UseCases\AcceptRequestUseCase;
use App\Application\Request\UseCases\FindRequestByIdUseCase;
use App\Application\Request\UseCases\ListPendingRequestsUseCase;
use App\Domain\TaxPayer\Enums\enFileType;
use App\Domain\Request\Enums\EnRequestStatus;
use App\Http\Responses\ApiResponse;
use App\Application\User\Services\UploadFileService;
use App\Domain\User\Enums\UserRole;
use App\Http\Requests\TaxPayerRequest\AcceptRequestOfTaxPayerRequest;
use Exception;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function __construct(
        private UploadFileService $uploadFileService
    ) {}

    public function index()
    {
        //
    }

    public function getPendingRequests(ListPendingRequestsUseCase $useCase)
    {
        $pendingRequests = $useCase->execute();
        return ApiResponse::ok(
            data: $pendingRequests,
            message: "تم جلب الطلبات قيد الإنتظار بنجاح."
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
                requestStatus: EnRequestStatus::Pending,
                note: $request->note
            );

            $result = $useCase->execute($taxPayerDTO);

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

            $result = $useCase->execute($requestId);
            return ApiResponse::ok(
                data: $result,
                message: "تم قبول الطلب مع ال ID [$requestId] بنجاح."
            );

        } catch (DomainException $e) {
            return ApiResponse::serverError([], $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id , FindRequestByIdUseCase $useCase)
    {
        $request = $useCase->execute($id);
        return ApiResponse::ok(
            data: $request,
            message: "تم جلب الطلب مع ال ID [$id] بنجاح."
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
