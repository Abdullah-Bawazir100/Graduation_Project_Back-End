<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;
use illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PaymentTypeModel extends Model
{
    use LogsActivity;
    protected $table = 'payment_types';
    protected $fillable = ['name' , 'note'];

    public function files()
    {
        return $this->hasMany(FileModel::class , 'payment_type_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('payment_type')
            ->logOnly(['name' , 'note'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء نوع سداد',
                'updated' => 'تحديث نوع سداد',
                'deleted' => 'حذف نوع سداد',
            });
    }
}
