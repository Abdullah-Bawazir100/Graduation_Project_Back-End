<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;
use illuminate\Database\Eloquent\Model;
use App\Infrastructure\Persistence\Eloquent\Models\UserModel;
use App\Infrastructure\Persistence\Eloquent\Traits\HasRecyclePin;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class DepartmentModel extends Model
{
    use LogsActivity , HasRecyclePin;
    protected $table = 'departments';
    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(UserModel::class, 'department_id');
    }


    public function files()
    {
        return $this->hasMany(FileModel::class, 'department_id');
    }

    public function fileMovement()
    {
        return $this->hasMany(FileMovementModel::class, 'department_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('department')
            ->logOnly(['name'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء قسم',
                'updated' => 'تحديث قسم',
                'deleted' => 'حذف قسم',
        });
    }
}
