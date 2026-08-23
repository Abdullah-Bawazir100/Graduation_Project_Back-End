<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use App\Infrastructure\Persistence\Eloquent\Traits\HasRecyclePin;
use illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class TaxInformationModel extends Model
{
    use LogsActivity , HasRecyclePin;
    protected $table = 'tax_informations';
    protected $fillable = [
        'tax_amount',
        'last_payment',
        'attachment',
        'file_id',
        'tax_type_id'
    ];

    public function taxType()
    {
        return $this->belongsTo(TaxTypeModel::class , 'tax_type_id');
    }

    public function MainFile()
    {
        return $this->belongsTo(FileModel::class , 'file_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('tax_information')
            ->logOnly(['tax_amount', 'last_payment', 'attachment' , 'file_id', 'tax_type_id'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء معلومة ضريبية',
                'updated' => 'تحديث معلومة ضريبية',
                'deleted' => 'حذف معلومة ضريبية',
        });
    }
}
