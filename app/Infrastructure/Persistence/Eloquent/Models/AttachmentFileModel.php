<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use App\Infrastructure\Persistence\Eloquent\Traits\HasRecyclePin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class AttachmentFileModel extends Model
{
    use LogsActivity , HasRecyclePin;

    protected $table = 'attachments';
    protected $fillable = ['title', 'attachment_file' , 'file_id'];

    public function file(): BelongsTo
    {
        return $this->belongsTo(FileModel::class , 'file_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('attachment')
            ->logOnly(['title' , 'attachment_file' , 'file_id'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء مرفق ملف',
                'updated' => 'تحديث مرفق ملف',
                'deleted' => 'حذف مرفق ملف',
            });
    }
}
