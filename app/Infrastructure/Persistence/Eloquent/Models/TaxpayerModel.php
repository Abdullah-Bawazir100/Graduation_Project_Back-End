<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\TaxPayer\Enums\enFileType;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class TaxPayerModel extends Model
{
    use Notifiable , LogsActivity;

    protected $table = 'tax_payers';

    protected $fillable = [
        'user_id',
        'commercial_record',
        'activity_license',
        'trade_pict',
        'insurance_card',
        'property_doc_pict',
        'file_type'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'file_type' => enFileType::class
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class , 'user_id');
    }

    public function companies()
    {
        return $this->hasMany(CompanyModel::class , 'tax_payer_id');
    }

    public function charitable_companies()
    {
        return $this->hasMany(CharitableCompanyModel::class , 'tax_payer_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('tax_payer')
            ->logOnly([
                'user_id',
                'commercial_record',
                'activity_license',
                'trade_pict',
                'insurance_card',
                'property_doc_pict',
                'file_type',
            ])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء مكلف',
                'updated' => 'تحديث مكلف',
                'deleted' => 'حذف مكلف',
            });
    }
}
