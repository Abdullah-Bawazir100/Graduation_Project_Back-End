<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use App\Infrastructure\Persistence\Eloquent\Traits\HasRecyclePin;
use illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class DistrictModel extends Model
{
    use LogsActivity , HasRecyclePin;
    protected $table = 'districts';
    protected $fillable = ['name' , 'region_id'];

    public function region()
    {
        return $this->belongsTo(
            RegionModel::class,
            'region_id'
        );
    }

    public function files()
    {
        return $this->hasMany(
            FileModel::class,
            'district_id'
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
