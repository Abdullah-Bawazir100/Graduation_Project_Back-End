<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use App\Infrastructure\Persistence\Eloquent\Traits\HasRecyclePin;
use illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class FileStatusModel extends Model
{
    use LogsActivity , HasRecyclePin;
    protected $table = 'file_status';
    protected $fillable = ['status_name' , 'status_description'];

    public function file()
    {
        return $this->hasMany(FileModel::class , 'file_status_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('file_status')
            ->logOnly(['status_name' , 'status_description'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء حالة ملف',
                'updated' => 'تحديث حالة ملف',
                'deleted' => 'حذف حالة ملف',
            });
    }
}
