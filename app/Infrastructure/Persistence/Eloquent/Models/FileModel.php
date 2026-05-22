<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class FileModel extends Model
{
    use LogsActivity;
    protected $table = 'files';
    protected $fillable = [
        'tax_number',
        'inventory_number',
        'activity_start_date',
        'docs_count',
        'note',

        'tax_payer_id',
        'department_id',
        'file_status_id',
        'activity_type_id',
        'payment_type_id',
        'region_id',
        'district_id',
        'created_by',
    ];

    public function taxPayer()
    {
        return $this->belongsTo(TaxPayerModel::class, 'tax_payer_id');

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

    public function region()
    {
        return $this->belongsTo(RegionModel::class, 'region_id');
    }

    public function district()
    {
        return $this->belongsTo(DistrictModel::class, 'district_id');
    }

    public function fileMovement()
    {
        return $this->hasMany(FileMovementModel::class, 'file_id');
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
                'tax_payer_id',
                'department_id',
                'file_status_id',
                'activity_type_id',
                'payment_type_id',
                'region_id',
                'district_id',
                'created_by',
            ])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء ملف',
                'updated' => 'تحديث ملف',
                'deleted' => 'حذف ملف',
            });
    }
}
