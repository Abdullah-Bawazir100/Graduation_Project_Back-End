<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use App\Domain\FileMovement\Enums\enFileMovement;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class FileMovementModel extends Model
{
    use LogsActivity;
    protected $table = 'file_movements';
    protected $fillable = [
        'file_id',
        'tax_collector_id',
        'status',
        'date',
        'department_id',
        'created_by',
    ];


    public function file()
    {
        return $this->belongsTo(FileModel::class, 'file_id');
    }

    public function taxCollector()
    {
        return $this->belongsTo(TaxCollectorModel::class, 'tax_collector_id');
    }

    public function department()
    {
        return $this->belongsTo(DepartmentModel::class, 'department_id');
    }

    public function creator()
    {
        return $this->belongsTo(UserModel::class, 'created_by');
    }

    protected $casts = [
        'status' => enFileMovement::class,
        'date' => 'date'
    ];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('file_movement')
            ->logOnly([
                'file_id',
                'tax_collector_id',
                'status',
                'date',
                'department_id',
                'created_by',
            ])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء حركة ملف',
                'updated' => 'تحديث حركة ملف',
                'deleted' => 'حذف حركة ملف',
            });
    }
}
