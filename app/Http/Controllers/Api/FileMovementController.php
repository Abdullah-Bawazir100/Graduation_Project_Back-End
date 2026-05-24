<?php

namespace App\Http\Controllers\Api;

use App\Application\FileMovement\DTOs\FileMovementDTOs;
use App\Application\FileMovement\UseCases\CreateFileMovementUseCase;
use App\Application\FileMovement\UseCases\DeleteFileMovementUseCase;
use App\Application\FileMovement\UseCases\FindFileMovementByIdUseCase;
use App\Application\FileMovement\UseCases\ListFilesMovementsUseCase;
use App\Application\FileMovement\UseCases\UpdateFileMovementUseCase;
use App\Domain\FileMovement\Enums\enFileMovement;
use App\Domain\FileMovement\Repositories\FileMovementRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\FileMovement\StoreFileMovementRequest;
use App\Http\Requests\FileMovement\UpdateFileMovementRequest;
use App\Http\Responses\ApiResponse;
use DomainException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FileMovementController extends Controller
{

    public function __construct(
        private FileMovementRepositoryInterface $file_movement_repository
    )
    {}


    public function index(ListFilesMovementsUseCase $useCase)
    {
        $filesMovements = $useCase->execute();
        return ApiResponse::ok(
            data: $filesMovements,
            message: "تم جلب حركات الملفات بنجاح."
        );
    }


    public function store(StoreFileMovementRequest $request , CreateFileMovementUseCase $useCase)
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
            $dto = new FileMovementDTOs(
                status: enFileMovement::from($request->status),
                date: $request->date,
                fileId: $request->fileId,
                taxCollectorId: $request->taxCollectorId,
                departmentId: $request->departmentId
            );
            // Execute the use case
            $result = $useCase->execute($dto, $authenticatedUser->id);

            return ApiResponse::created(
                data: $result,
                message: 'تم إنشاء حركة الملف بنجاح.'
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


    public function show(int $id , FindFileMovementByIdUseCase $useCase)
    {
        $fileMovement = $useCase->execute($id);
        return ApiResponse::ok(
            data: $fileMovement,
            message: "تم جلب حركة الملف مع ال ID [$id] بنجاح."
        );
    }

    public function update(int $id , UpdateFileMovementRequest $request ,
    UpdateFileMovementUseCase $useCase)
    {
        try {

            $existingFileMovement = $this->file_movement_repository->findById($id);
            if(!$existingFileMovement)
            {
                return ApiResponse::notFound(
                    message: "حركة الملف مع ال ID [$id] غير موجودة."
                );
            }

            $dto = new FileMovementDTOs(
                status: enFileMovement::from($request->status),
                date: $request->date,
                fileId: $request->fileId,
                taxCollectorId: $request->taxCollectorId,
                departmentId: $request->departmentId,
            );

            $result = $useCase->execute($dto , $id);

            return ApiResponse::ok(
                data: $result,
                message: "تم تحديث بيانات حركة الملف مع ال ID [$id] بنجاح."
            );


        } catch (DomainException $e) {
            return ApiResponse::notFound([], $e->getMessage());
        } catch (Exception $e) {
            Log::error('File Movement update error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return ApiResponse::serverError($e->getMessage());
        }
    }


    public function destroy(int $id , DeleteFileMovementUseCase $useCase)
    {
        $useCase->execute($id);
        return ApiResponse::ok(
            [] ,
            "تم حذف حركة الملف مع ال ID [$id] بنجاح."
        );
    }
}
