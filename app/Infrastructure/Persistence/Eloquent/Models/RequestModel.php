<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use App\Domain\Request\Enums\EnRequestStatus;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Persistence\Eloquent\Models\JobTypeModel;
use App\Infrastructure\Persistence\Eloquent\Models\DepartmentModel;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class RequestModel extends Model
{
    use HasApiTokens , Notifiable , LogsActivity;

    protected $table = 'requests';

    protected $fillable = [
        'user_id',
        'trade_name',
        'commercial_record',
        'activity_license',
        'trade_pict',
        'insurance_card',
        'property_doc_pict',
        'file_type',
        'articles_of_incorporation',
        'govemor_license',
        'partners_id_cards',
        'by_laws_copy',
        'status',
        'note'
    ];

    public function  user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    protected $casts = [
        'status' => EnRequestStatus::class
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('request')
            ->logOnly([
                'user_id',
                'trade_name',
                'commercial_record',
                'activity_license',
                'trade_pict',
                'insurance_card',
                'property_doc_pict',
                'file_type',
                'articles_of_incorporation',
                'govemor_license',
                'partners_id_cards',
                'by_laws_copy',
                'status',
                'note'
            ])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء طلب',
                'updated' => 'تحديث طلب',
                'deleted' => 'حذف طلب',
            });
    }
}
