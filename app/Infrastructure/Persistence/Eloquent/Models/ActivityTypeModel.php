<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use App\Infrastructure\Persistence\Eloquent\Traits\HasRecyclePin;
use illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ActivityTypeModel extends Model
{
    use LogsActivity , HasRecyclePin;
    protected $table = 'activity_types';
    protected $fillable = ['name'];

    public function files()
    {
        return $this->hasMany(FileModel::class , 'activity_type_id');

    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('activity_type')
            ->logOnly(['name'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء نوع نشاط',
                'updated' => 'تحديث نوع نشاط',
                'deleted' => 'حذف نوع نشاط',
            });
    }
}
