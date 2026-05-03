<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;
use illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class DistrictModel extends Model
{
    use LogsActivity;
    protected $table = 'districts';
    protected $fillable = ['name' , 'region_id'];

    public function region()
    {
        return $this->belongsTo(
            RegionModel::class,
            'region_id'
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('district')
            ->logOnly(['name' , 'region_id'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء حي ',
                'updated' => 'تحديث حي',
                'deleted' => 'حذف حي',
            });
    }
}
