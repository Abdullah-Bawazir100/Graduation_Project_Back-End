<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\TaxPayer\Enums\enFileType;
use App\Infrastructure\Persistence\Eloquent\Traits\HasRecyclePin;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class TaxPayerModel extends Model
{
    use Notifiable , LogsActivity , HasRecyclePin;

    protected $table = 'tax_payers';

    protected $fillable = [
        'user_id',
        'trade_name',
        'commercial_record',
        'activity_license',
        'trade_pict',
        'insurance_card',
        'property_doc_pict',
        'file_type',
        'source'
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

    public function tax_informations()
    {
        return $this->hasMany(TaxInformationModel::class , 'tax_payer_id');
    }

    public function file()
    {
        return $this->hasOne(FileModel::class , 'tax_payer_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($taxPayer) {
            $taxPayer->companies()->each(function ($company) {
                $company->delete();
            });
            $taxPayer->charitable_companies()->each(function ($charitableCompany) {
                $charitableCompany->delete();
            });
            $taxPayer->tax_informations()->each(function ($taxInfo) {
                $taxInfo->delete();
            });
            if ($taxPayer->file) {
                $taxPayer->file->delete();
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('tax_payer')
            ->logOnly([
                'user_id',
                'trade_name',
                'commercial_record',
                'activity_license',
                'trade_pict',
                'insurance_card',
                'property_doc_pict',
                'file_type',
                'source'
            ])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء مكلف',
                'updated' => 'تحديث مكلف',
                'deleted' => 'حذف مكلف',
            });
    }
}
