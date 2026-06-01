<?php

namespace App\Http\Controllers\Api;

use App\Application\RecyclePin\UseCases\FindRecyclePinByIdUseCase;
use App\Application\RecyclePin\UseCases\DeleteRecyclePinUseCase;
use App\Application\RecyclePin\UseCases\ListRecyclePinsUseCase;
use App\Application\RecyclePin\UseCases\RestoreRecyclePinUseCase;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Exception;

class RecyclePinController extends Controller
{
    public function index(ListRecyclePinsUseCase $useCase)
    {
        try {
            $records = $useCase->execute();

            $formattedRecords = array_map(function($record) {
                return [
                    'id' => $record->id,
                    'type' => class_basename($record->type),
                    'data' => $record->data,
                    'userId' => $record->userId,
                    'createdAt' => $record->createdAt,
                ];
            }, $records);

            return ApiResponse::ok(
                data: $formattedRecords,
                message: 'تم جلب سجلات سلة المحذوفات بنجاح'
            );

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب سجلات سلة المحذوفات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id , FindRecyclePinByIdUseCase $useCase)
    {
        try {
            $record = $useCase->execute($id);
            return response()->json([
                'success' => true,
                'message' => 'تم جلب السجل بنجاح',
                'data' => [
                    'id' => $record->id,
                    'type' => class_basename($record->type),
                    'data' => $record->data,
                    'userId' => $record->userId,
                    'createdAt' => $record->createdAt,
                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'السجل غير موجود أو حدث خطأ',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function restore(int $id , RestoreRecyclePinUseCase $useCase)
    {
        try {
            $useCase->execute($id);
            return ApiResponse::ok(
                message: 'تم استعادة السجل بنجاح'
            );
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء استعادة السجل',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id , DeleteRecyclePinUseCase $useCase)
    {
        try {
            $useCase->execute($id);
            return response()->json([
                'success' => true,
                'message' => 'تم حذف السجل من سلة المحذوفات نهائياً',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف السجل',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

