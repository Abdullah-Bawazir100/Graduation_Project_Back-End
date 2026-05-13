<?php

namespace App\Http\Controllers\Api;

use App\Application\File\DTOs\FileDTOs;
use App\Application\File\UseCases\CreateFileUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\File\StoreFileRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function __construct(
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
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
                fullAddress: null,
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
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
