<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;
use illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class TaxTypeModel extends Model
{
    use LogsActivity;
    protected $table = 'tax_types';
    protected $fillable = ['name'];

    public function taxInformations()
    {
        return $this->hasMany(TaxInformationModel::class , 'tax_type_id');
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
