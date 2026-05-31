<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\RecyclePin\Entities\RecyclePin;
use App\Domain\RecyclePin\Repositories\RecyclePinRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\RecyclePinModel;

class RecyclePinRepository implements RecyclePinRepositoryInterface
{
    public function getAll(): array
    {
        return RecyclePinModel::orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($model) => new RecyclePin(
                $model->id,
                $model->type,
                $model->data,
                $model->user_id,
                $model->created_at ? $model->created_at->format('Y-m-d H:i:s') : null
            ))
            ->toArray();
    }

    public function findById(int $id): ?RecyclePin
    {
        $model = RecyclePinModel::find($id);

        if (!$model) {
            return null;
        }

        return new RecyclePin(
            $model->id,
            $model->type,
            $model->data,
            $model->user_id,
            $model->created_at ? $model->created_at->format('Y-m-d H:i:s') : null
        );
    }

    public function delete(int $id): void
    {
        RecyclePinModel::where('id', $id)->delete();
    }
}
