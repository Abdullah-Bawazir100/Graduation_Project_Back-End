<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;
use illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class RegionModel extends Model
{
    use LogsActivity;
    protected $table = 'regions';
    protected $fillable = ['name'];
    public function districts()
    {
        return $this->hasMany(
            DistrictModel::class,
            'region_id',
            'id'
        );
    }

    public function files()
    {
        return $this->hasMany(
            FileModel::class,
            'region_id',
        );
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('region')
            ->logOnly(['name'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء منطقة ',
                'updated' => 'تحديث منطقة',
                'deleted' => 'حذف منطقة',
            });
    }
}
