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

            $actionTranslations = [
                'UserModel' => 'مستخدم',
                'TaxTypeModel' => 'نوع ضريبة',
                'TaxPayerModel' => 'مكلف',
                'TaxInformationModel' => 'معلومات ضريبية',
                'TaxCollectorModel' => 'مأمور',
                'RequestModel' => 'طلب',
                'RegionModel' => 'منطقة',
                'PaymentTypeModel' => 'نوع دفع',
                'NotificationModel' => 'إشعار',
                'JobTypeModel' => 'نوع وظيفة',
                'FileStatusModel' => 'حالة ملف',
                'FileMovementModel' => 'حركة ملف',
                'FileModel' => 'ملف',
                'DistrictModel' => 'حي',
                'DepartmentModel' => 'قسم',
                'CompanyModel' => 'ملف شركة',
                'CharitableCompanyModel' => 'ملف شركة خيرية',
                'AttachmentFileModel' => 'مرفق',
                'AddressModel' => 'عنوان',
                'ActivityTypeModel' => 'نوع نشاط',
            ];

            $formattedRecords = array_map(function($record) use ($actionTranslations) {
                $translatedAction = 'حذف ' . ($actionTranslations[$record->modelName] ?? 'سجل');
                return [
                    'user' => [
                        'id' => $record->user['id'] ?? null,
                        'name' => isset($record->user) ? trim(($record->user['first_name'] ?? '') . ' ' . ($record->user['last_name'] ?? '')) : 'نظام',
                        'role' => $record->user['role'] ?? '—',
                    ],
                    'action' => $translatedAction,
                    'deleted_record_id' => $record->data['id'] ?? null,
                    'recycle_pin_id' => $record->id,
                    'model' => $record->modelName,
                    //'details' => $record->data,
                    'datetime' => $record->createdAt,
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

            $actionTranslations = [
                'UserModel' => 'مستخدم',
                'TaxTypeModel' => 'نوع ضريبة',
                'TaxPayerModel' => 'مكلف',
                'TaxInformationModel' => 'معلومات ضريبية',
                'TaxCollectorModel' => 'مأمور',
                'RequestModel' => 'طلب',
                'RegionModel' => 'منطقة',
                'PaymentTypeModel' => 'نوع دفع',
                'NotificationModel' => 'إشعار',
                'JobTypeModel' => 'نوع وظيفة',
                'FileStatusModel' => 'حالة ملف',
                'FileMovementModel' => 'حركة ملف',
                'FileModel' => 'ملف',
                'DistrictModel' => 'حي',
                'DepartmentModel' => 'قسم',
                'CompanyModel' => 'ملف شركة',
                'CharitableCompanyModel' => 'ملف شركة خيرية',
                'AttachmentFileModel' => 'مرفق',
                'AddressModel' => 'عنوان',
                'ActivityTypeModel' => 'نوع نشاط',
            ];

            $translatedAction = 'حذف ' . ($actionTranslations[$record->modelName] ?? 'سجل');

            return response()->json([
                'success' => true,
                'message' => 'تم جلب السجل بنجاح',
                'data' => [
                    'user' => [
                        'id' => $record->user['id'] ?? null,
                        'name' => isset($record->user) ? trim(($record->user['first_name'] ?? '') . ' ' . ($record->user['last_name'] ?? '')) : 'نظام',
                        'role' => $record->user['role'] ?? '—',
                    ],
                    'action' => $translatedAction,
                    'deleted_record_id' => $record->data['id'] ?? null,
                    'recycle_pin_id' => $record->id,
                    'model' => $record->modelName,
                    //'details' => $record->data,
                    'datetime' => $record->createdAt,
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
                'message' => 'تعذر استعادة السجل بسبب وجود مراجع أو بيانات مرتبطة محذوفة. يرجى استعادة البيانات المرتبطة أولًا قبل استعادة هذا السجل.',                'error' => $e->getMessage()
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

