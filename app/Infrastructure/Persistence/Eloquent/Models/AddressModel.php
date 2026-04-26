<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class AddressModel extends Model
{
    use LogsActivity;
    
    protected $table = 'addresses';
    protected $fillable = ['address', 'district_id'];

    public function district()
    {
        return $this->belongsTo(
            DistrictModel::class,
            'district_id'
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('address')
            ->logOnly(['address'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء عنوان جديد',
                'updated' => 'تحديث العنوان',
                'deleted' => 'حذف العنوان',
            });
    }
}