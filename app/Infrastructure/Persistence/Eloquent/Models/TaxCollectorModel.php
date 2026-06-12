<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Persistence\Eloquent\Models\JobTypeModel;
use App\Infrastructure\Persistence\Eloquent\Models\DepartmentModel;
use App\Infrastructure\Persistence\Eloquent\Traits\HasRecyclePin;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class TaxCollectorModel extends Model
{
    use HasApiTokens , Notifiable , LogsActivity , HasRecyclePin;

    protected $table = 'tax_collectors';

    protected $fillable = [
        'full_name',
        'id_card',
        'phone',
        'job_type_id',
        'dept_id',
    ];

    public function jobType()
    {
        return $this->belongsTo(JobTypeModel::class, 'job_type_id');
    }

    public function department()
    {
        return $this->belongsTo(DepartmentModel::class, 'dept_id');
    }

    public function fileMovement()
    {
        return $this->hasMany(FileMovementModel::class, 'tax_collector_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($taxCollector) {
            $taxCollector->fileMovement()->each(function ($movement) {
                $movement->delete();
            });
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('tax_collector')
            ->logOnly([
                'full_name',
                'id_card',
                'phone',
                'job_type_id',
                'dept_id',
            ])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء مأمور',
                'updated' => 'تحديث مأمور',
                'deleted' => 'حذف مأمور',
            });
    }
}
