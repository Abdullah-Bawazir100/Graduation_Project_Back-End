<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Persistence\Eloquent\Models\JobTypeModel;
use App\Infrastructure\Persistence\Eloquent\Models\DepartmentModel;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class TaxCollectorModel extends Model
{
    use HasApiTokens , Notifiable , LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('tax_collector')
            ->logOnly([
                'first_name',
                'last_name',
                'user_name',
                'department_id',
                'role',
            ])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء جامع ضرائب',
                'updated' => 'تحديث جامع ضرائب',
                'deleted' => 'حذف جامع ضرائب',
            });
    }
}
