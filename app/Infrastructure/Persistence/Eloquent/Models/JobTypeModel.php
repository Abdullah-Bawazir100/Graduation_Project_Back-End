<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use App\Infrastructure\Persistence\Eloquent\Traits\HasRecyclePin;
use illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class JobTypeModel extends Model
{
    use LogsActivity , HasRecyclePin;
    protected $table = 'job_types';
    protected $fillable = ['name'];

    public function taxCollectors()
    {
        return $this->hasMany(TaxCollectorModel::class , 'job_type_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($jobType) {
            $jobType->taxCollectors()->each(function ($collector) {
                $collector->delete();
            });
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('job_type')
            ->logOnly(['name'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء نوع وظيفة',
                'updated' => 'تحديث نوع وظيفة',
                'deleted' => 'حذف نوع وظيفة',
        });
    }
}
