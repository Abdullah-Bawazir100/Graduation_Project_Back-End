<?php

namespace App\Application\RecyclePin\UseCases;

use App\Domain\RecyclePin\Repositories\RecyclePinRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class RestoreRecyclePinUseCase
{

    public function __construct(
        private RecyclePinRepositoryInterface $repository
    )
    {
    }

    public function execute(int $id): bool
    {
        $recyclePin = $this->repository->findById($id);

        if (!$recyclePin) {
            throw new Exception("لا يوجد سجل في سلة المحذوفات مع ال ID [$id].");
        }

        try {
            DB::beginTransaction();

            $modelClass = $recyclePin->type;

            if (!class_exists($modelClass)) {
                throw new Exception("المودل [$modelClass] غير موجودة.");
            }

            $model = new $modelClass();

            $data = $recyclePin->data;
            
            // تعويض كلمة المرور المفقودة للسجلات القديمة لتجنب خطأ قاعدة البيانات
            if ($modelClass === \App\Infrastructure\Persistence\Eloquent\Models\UserModel::class && !isset($data['password'])) {
                $data['password'] = \Illuminate\Support\Facades\Hash::make('12345678');
            }

            // Re-insert data into the model (unguarded to avoid fillable restrictions)
            $model->forceFill($data);

            // Temporarily disable auto-incrementing so Eloquent inserts the original ID
            $model->incrementing = false;

            // Disable timestamps update so the original created_at and updated_at are preserved
            $model->timestamps = false;

            $model->save();

            // Optionally, delete from recycle pin after restore
            $this->repository->delete($id);

            DB::commit();
            return true;

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            // 23000 is Integrity constraint violation, 1452 is foreign key constraint
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'foreign key constraint fails')) {
                throw new Exception("لا يمكن استعادة السجل لوجود بيانات مرتبطة به مفقودة (مثل القسم أو المستخدم). يرجى استعادة السجل الرئيسي أولاً.");
            }
            throw new Exception("حدث خطأ في قاعدة البيانات أثناء الاستعادة: " . $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
