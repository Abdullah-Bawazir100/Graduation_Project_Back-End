<?php

namespace App\Http\Controllers\Api;

use App\Application\FileMovement\DTOs\FileMovementDTOs;
use App\Application\FileMovement\UseCases\CreateFileMovementUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\FileMovement\StoreFileMovementRequest;
use App\Http\Responses\ApiResponse;
use DomainException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileMovementController extends Controller
{

    public function index()
    {
        //
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
                status: $request->status,
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


    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
}
