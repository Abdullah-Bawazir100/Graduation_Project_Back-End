<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;
use illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class TaxInformationModel extends Model
{
    use LogsActivity;
    protected $table = 'tax_informations';
    protected $fillable = [
        'tax_amount',
        'last_payment',
        'tax_payer_id',
        'tax_type_id'
    ];

    public function taxType()
    {
        return $this->belongsTo(TaxTypeModel::class , 'tax_type_id');
    }

    public function taxPayer()
    {
        return $this->belongsTo(TaxPayerModel::class , 'tax_payer_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('tax_information')
            ->logOnly(['tax_amount', 'last_payment', 'tax_payer_id', 'tax_type_id'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء معلومة ضريبية',
                'updated' => 'تحديث معلومة ضريبية',
                'deleted' => 'حذف معلومة ضريبية',
        });
    }
}
