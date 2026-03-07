<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Infrastructure\Persistence\Eloquent\Models\DepartmentModel;

class UserModel extends Authenticatable
{
    use HasApiTokens , Notifiable;

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
}