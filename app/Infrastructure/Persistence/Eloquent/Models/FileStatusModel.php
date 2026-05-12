<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;
use illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class FileStatusModel extends Model
{
    use LogsActivity;
    protected $table = 'file_status';
    protected $fillable = ['status_name' , 'status_description'];


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
