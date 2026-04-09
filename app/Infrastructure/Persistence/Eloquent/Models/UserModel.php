<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use App\Domain\User\Enums\UserRole;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Infrastructure\Persistence\Eloquent\Models\DepartmentModel;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
class UserModel extends Authenticatable
{
    use HasApiTokens , Notifiable , LogsActivity;

    protected $table = 'app_users';

    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'id_card',
        'user_name',
        'phone',
        'password',
        'created_by',
        'department_id',
        'role',
        'must_change_password',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'role' => UserRole::class
    ];

    public function department()
    {
        return $this->belongsTo(
            DepartmentModel::class,
            'department_id'
        );
    }

    public function creator()
    {
        return $this->belongsTo(
            UserModel::class,
            'created_by'
        );
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('user')
            ->logOnly([
                'first_name',
                'last_name',
                'user_name',
                'department_id',
                'role',
            ])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'إنشاء مستخدم',
                'updated' => 'تحديث مستخدم',
                'deleted' => 'حذف مستخدم',
            });
    }
}
