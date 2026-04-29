<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;
use illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class JobTypeModel extends Model
{
    use LogsActivity;
    protected $table = 'job_types';
    protected $fillable = ['name'];

    // public function users()
    // {
    //     return $this->hasMany(UserModel::class, 'department_id');
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('job_type')
            ->logOnly(['name'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء نوع وظيفة',
                'updated' => 'تحديث نوع وظيفة',
                'deleted' => 'حذف نوع وظيفة',
        });
    }
}
