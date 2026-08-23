<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use App\Infrastructure\Persistence\Eloquent\Traits\HasRecyclePin;
use illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class RegionModel extends Model
{
    use LogsActivity , HasRecyclePin;
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

    public function taxPayers()
    {
        return $this->hasMany(
            TaxPayerModel::class,
            'region_id',
        );
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($region) {
            $region->districts()->each(function ($district) {
                $district->delete();
            });
        });
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
