<?php

namespace App\Http\Controllers\Api;

use App\Application\File\DTOs\FileDTOs;
use App\Application\File\UseCases\CreateFileUseCase;
use App\Application\File\UseCases\DeleteFileUseCase;
use App\Application\File\UseCases\FindFileByIdUseCase;
use App\Application\File\UseCases\ListFilesUseCase;
use App\Application\File\UseCases\UpdateFileUseCase;
use App\Application\File\UseCases\GenerateFileReportUseCase;
use App\Application\File\UseCases\GenerateBulkFilesReportUseCase;
use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\File\StoreFileRequest;
use App\Http\Requests\File\UpdateFileRequest;
use App\Http\Responses\ApiResponse;
use DomainException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    public function __construct(
        private FileRepositoryInterface $file_repository
    ) {}


    public function index(Request $request , ListFilesUseCase $useCase)
    {
        $search = $request->query('search');
        $files = $useCase->execute($search, Auth::id());
        return ApiResponse::ok($files , "تم جلب الملفات بنجاح.");
    }

    public function store(StoreFileRequest $request , CreateFileUseCase $useCase)
    {
        try {
            // Get the authenticated user ID
            $authenticatedUser = Auth::user();

            if (!$authenticatedUser) {
                return response()->json([
                    'error' => 'المستخدم غير مسجل الدخول'
                ], 401);
            }

            // Prepare DTO from request data
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
                districtId:  $request->districtId,
                requestId: $request->requestId
            );
            // Execute the use case
            $result = $useCase->execute($dto, $authenticatedUser->id);

            return ApiResponse::created(
                data: $result,
                message: 'تم إنشاء الملف بنجاح.'
            );

        } catch (DomainException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'حدث خطأ في الخادم، يرجى المحاولة لاحقاً.'
            ], 500);
        }
    }


    public function show(int $id , FindFileByIdUseCase $useCase)
    {
        $file = $useCase->execute($id, Auth::id());
        return ApiResponse::ok(
            data: $file,
            message: "تم جلب الملف بنجاح."
        );
    }


    public function update(int $id , UpdateFileRequest $request , UpdateFileUseCase $useCase)
    {
        try {

            $existingFile = $this->file_repository->findById($id);
            if(!$existingFile)
            {
                return ApiResponse::notFound(
                    message: "الملف مع ال ID [$id] غير موجود."
                );
            }

            $dto = new FileDTOs(
                taxNumber: $request->taxNumber,
                inventoryNumber: $request->inventoryNumber,
                activityStartDate: $request->activityStartDate,
                docsCount: $request->docsCount,
                note: $request->note,
                taxPayerId: $request->taxPayerId,
                departmentId: $request->departmentId,
                fileStatusId: $request->fileStatusId,
                activityTypeId: $request->activityTypeId,
                paymentTypeId: $request->paymentTypeId,
                regionId: $request->regionId,
                districtId: $request->districtId,
            );

            $result = $useCase->execute($dto , $id, Auth::id());

            return ApiResponse::ok(
                data: $result,
                message: "تم تحديث بيانات الملف مع ال ID [$id] بنجاح."
            );


        } catch (DomainException $e) {
            return ApiResponse::notFound([], $e->getMessage());
        } catch (Exception $e) {
            Log::error('File update error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return ApiResponse::serverError($e->getMessage());
        }
    }


    public function destroy(int $id , DeleteFileUseCase $useCase)
    {
        $useCase->execute($id);
        return ApiResponse::ok(
            message: "تم حذف الملف مع ال ID [$id] بنجاح."
        );
    }

    public function generateReport(int $id, GenerateFileReportUseCase $useCase)
    {
        try {
            $pdfUrl = $useCase->execute($id, Auth::id());
            return ApiResponse::ok(
                data: ['report_url' => $pdfUrl],
                message: "تم إنشاء تقرير الملف بنجاح."
            );
        } catch (DomainException $e) {
            return ApiResponse::notFound([], $e->getMessage());
        } catch (Exception $e) {
            Log::error('File report generation error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return ApiResponse::serverError('حدث خطأ أثناء إنشاء التقرير.');
        }
    }
    public function generateBulkFilesReport(Request $request, GenerateBulkFilesReportUseCase $useCase)
    {
        $request->validate([
            'activityTypeId' => 'nullable|integer',
            'regionId' => 'nullable|integer',
            'districtId' => 'nullable|integer',
        ]);

        try {
            $pdfUrl = $useCase->execute(
                Auth::id(),
                $request->activityTypeId,
                $request->regionId,
                $request->districtId
            );

            $message = "تم إنشاء التقرير الشامل للملفات بنجاح.";
            if ($request->has('activity_type_id') && $request->activity_type_id) {
                $message = "تم إنشاء تقرير الملفات بناءً على نوع النشاط بنجاح.";
            } elseif ($request->has('region_id') && $request->region_id && $request->has('district_id') && $request->district_id) {
                $message = "تم إنشاء تقرير الملفات بناءً على المنطقة و الحي بنجاح.";
            } elseif ($request->has('region_id') && $request->region_id) {
                $message = "تم إنشاء تقرير الملفات بناءً على المنطقة بنجاح.";
            }

            return ApiResponse::ok(
                data: ['report_url' => $pdfUrl],
                message: $message
            );
        } catch (DomainException $e) {
            return ApiResponse::notFound([], $e->getMessage());
        } catch (Exception $e) {
            Log::error('Bulk Files report error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return ApiResponse::serverError('حدث خطأ أثناء إنشاء التقرير.');
        }
    }
}
