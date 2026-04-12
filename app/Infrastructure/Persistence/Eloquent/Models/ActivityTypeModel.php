<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;
use illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ActivityTypeModel extends Model
{
    use LogsActivity;
    protected $table = 'activity_types';
    protected $fillable = ['name'];

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->useLogName('department')
    //         ->logOnly(['name'])
    //         ->logOnlyDirty()
    //         ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
    //             'created' => 'إنشاء قسم',
    //             'updated' => 'تحديث قسم',
    //             'deleted' => 'حذف قسم',
    //         });
    // }
}
