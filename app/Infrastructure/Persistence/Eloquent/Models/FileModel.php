<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use App\Infrastructure\Persistence\Eloquent\Traits\HasRecyclePin;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class FileModel extends Model
{
    use LogsActivity , HasRecyclePin;
    protected $table = 'files';
    protected $fillable = [
        'tax_number',
        'inventory_number',
        'activity_start_date',
        'docs_count',
        'note',

        'department_id',
        'file_status_id',
        'activity_type_id',
        'payment_type_id',
        'created_by',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function taxPayers()
    {
        return $this->hasMany(TaxPayerModel::class, 'file_id');
    }

    public function tax_informations()
    {
        return $this->hasOne(TaxInformationModel::class , 'file_id');
    }

    public function department()
    {
        return $this->belongsTo(DepartmentModel::class, 'department_id');
    }

    public function fileStatus()
    {
        return $this->belongsTo(FileStatusModel::class, 'file_status_id');
    }

    public function activityType()
    {
        return $this->belongsTo(ActivityTypeModel::class, 'activity_type_id');
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentTypeModel::class, 'payment_type_id');
    }

    public function creator()
    {
        return $this->belongsTo(UserModel::class, 'created_by');
    }

    public function fileMovement()
    {
        return $this->hasMany(FileMovementModel::class, 'file_id');
    }

    public function attachment()
    {
        return $this->hasOne(AttachmentFileModel::class, 'file_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($file) {
            $file->fileMovement()->each(function ($movement) {
                $movement->delete();
            });

            if ($file->attachment) {
                $file->attachment->delete();
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('file')
            ->logOnly([
                'tax_number',
                'inventory_number',
                'activity_start_date',
                'docs_count',
                'note',
                'department_id',
                'file_status_id',
                'activity_type_id',
                'payment_type_id',
                'created_by',
                'user_id',
            ])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء ملف',
                'updated' => 'تحديث ملف',
                'deleted' => 'حذف ملف',
            });
    }
}
