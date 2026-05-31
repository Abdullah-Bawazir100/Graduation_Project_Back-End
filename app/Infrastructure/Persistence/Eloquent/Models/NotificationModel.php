<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use App\Domain\Notification\Enums\enNotificationType;
use App\Infrastructure\Persistence\Eloquent\Traits\HasRecyclePin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class NotificationModel extends Model
{
    use Notifiable , LogsActivity , HasRecyclePin;

    protected $table = 'notifications';

    protected $fillable = [
        'title',
        'description',
        'notification_type',
        'receiver_phone',
        'send_by'
    ];

    protected $casts = [
        'notification_type' => enNotificationType::class
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class , 'send_by');
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('notification')
            ->logOnly([
                'title',
                'description',
                'notification_type',
                'receiver_phone',
                'send_by'
            ])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء إشعار',
                'updated' => 'تحديث إشعار',
                'deleted' => 'حذف إشعار',
            });
    }
}
