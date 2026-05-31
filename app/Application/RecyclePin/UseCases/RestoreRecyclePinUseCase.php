<?php

namespace App\Application\RecyclePin\UseCases;

use App\Domain\RecyclePin\Repositories\RecyclePinRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class RestoreRecyclePinUseCase
{
    private RecyclePinRepositoryInterface $repository;

    public function __construct(RecyclePinRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): bool
    {
        $recyclePin = $this->repository->findById($id);

        if (!$recyclePin) {
            throw new Exception("Recycle pin record not found.");
        }

        try {
            DB::beginTransaction();

            $modelClass = $recyclePin->type;
            
            if (!class_exists($modelClass)) {
                throw new Exception("Model class {$modelClass} not found.");
            }

            $model = new $modelClass();
            
            // Re-insert data into the model (unguarded to avoid fillable restrictions)
            $model->forceFill($recyclePin->data);

            // Temporarily disable auto-incrementing so Eloquent inserts the original ID
            $model->incrementing = false;
            
            // Disable timestamps update so the original created_at and updated_at are preserved
            $model->timestamps = false;
            
            $model->save();

            // Optionally, delete from recycle pin after restore
            $this->repository->delete($id);

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
