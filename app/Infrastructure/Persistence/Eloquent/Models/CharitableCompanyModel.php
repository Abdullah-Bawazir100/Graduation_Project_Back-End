<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class CharitableCompanyModel extends Model
{
    use HasFactory , LogsActivity;

    protected $table = 'charitable_companies';

    protected $fillable = [
        'tax_payer_id',
        'by_laws_copy',
    ];

    protected $casts = [
        'tax_payer_id' => 'integer',
    ];

    public function taxPayer(): BelongsTo
    {
        return $this->belongsTo(TaxPayerModel::class , 'tax_payer_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('charitable_company')
            ->logOnly([
                'tax_payer_id',
                'by_laws_copy'
            ])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء مكلف مع ملف شركة خيرية',
                'updated' => 'تحديث مكلف مع ملف شركة خيرية',
                'deleted' => 'حذف مكلف مع ملف شركة خيرية',
            });
    }
}
