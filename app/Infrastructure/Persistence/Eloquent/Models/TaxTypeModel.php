<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use App\Infrastructure\Persistence\Eloquent\Traits\HasRecyclePin;
use illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class TaxTypeModel extends Model
{
    use LogsActivity , HasRecyclePin;
    protected $table = 'tax_types';
    protected $fillable = ['name'];

    public function taxInformations()
    {
        return $this->hasMany(TaxInformationModel::class , 'tax_type_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($taxType) {
            $taxType->taxInformations()->each(function ($taxInfo) {
                $taxInfo->delete();
            });
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('tax_type')
            ->logOnly(['name'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء نوع ضريبة',
                'updated' => 'تحديث نوع ضريبة',
                'deleted' => 'حذف نوع ضريبة',
        });
    }
}
