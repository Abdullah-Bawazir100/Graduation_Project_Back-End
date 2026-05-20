<?php

namespace App\Http\Controllers\Api;

use App\Application\File\DTOs\FileDTOs;
use App\Application\File\UseCases\CreateFileUseCase;
use App\Application\File\UseCases\DeleteFileUseCase;
use App\Application\File\UseCases\FindFileByIdUseCase;
use App\Application\File\UseCases\ListFilesUseCase;
use App\Application\File\UseCases\UpdateFileUseCase;
use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\File\StoreFileRequest;
use App\Http\Requests\File\UpdateFileRequest;
use App\Http\Responses\ApiResponse;
use DomainException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    public function __construct(
        private FileRepositoryInterface $file_repository
    ) {}


    public function index(ListFilesUseCase $useCase)
    {
        $files = $useCase->execute();
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
                districtId:  $request->districtId
            );
            // Execute the use case
            $result = $useCase->execute($dto, $authenticatedUser->id);

            return ApiResponse::created(
                data: $result,
                message: 'تم إنشاء الملف بنجاح.'
            );

        } catch (\DomainException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'حدث خطأ في الخادم، يرجى المحاولة لاحقاً.'
            ], 500);
        }
    }


    public function show(int $id , FindFileByIdUseCase $useCase)
    {
        $file = $useCase->execute($id);
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

            $result = $useCase->execute($dto , $id);

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
            message: "تم حذف الملف مع ال ID $id بنجاح."
        );
    }
}
