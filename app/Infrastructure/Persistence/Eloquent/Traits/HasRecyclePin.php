<?php

namespace App\Infrastructure\Persistence\Eloquent\Traits;

use App\Infrastructure\Persistence\Eloquent\Models\RecyclePinModel;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 * @method static void deleting(\Closure|string|array $callback)
 */
trait HasRecyclePin
{
    /**
     * Boot the trait to hook into the model's deleting event.
     */
    protected static function bootHasRecyclePin()
    {
        static::deleting(function ($model) {

            $userId = Auth::id();
            if ($userId) {
                RecyclePinModel::create([
                    'type' => get_class($model),
                    'data' => $model->toArray(),
                    'user_id' => $userId,
                ]);
            }
        });
    }
}
