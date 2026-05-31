<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;
use illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class RecyclePinModel extends Model
{
    use LogsActivity;
    protected $table = 'recycle_pin';
    protected $fillable = ['type' , 'data' , 'user_id'];

    protected $casts = [
        'data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class , 'user_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('recycle_pin')
            ->logOnly(['type', 'user_id'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء سجل في سلة المحذوفات',
                'updated' => 'تحديث سجل في سلة المحذوفات',
                'deleted' => 'حذف سجل من سلة المحذوفات',
        });
    }
}
