<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class CompanyModel extends Model
{
    use HasFactory , LogsActivity;

    protected $table = 'companies';

    protected $fillable = [
        'tax_payer_id',
        'articles_of_incorporation',
        'govemor_license',
        'partners_id_cards',
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
            ->useLogName('company')
            ->logOnly([
                'tax_payer_id',
                'articles_of_incorporation',
                'govemor_license',
                'partners_id_cards',
            ])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء مكلف مع ملف شركة',
                'updated' => 'تحديث مكلف مع ملف شركة',
                'deleted' => 'حذف مكلف مع ملف شركة',
            });
    }
}
