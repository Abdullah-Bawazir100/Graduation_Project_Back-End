<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;
use illuminate\Database\Eloquent\Model;
use App\Infrastructure\Persistence\Eloquent\Models\UserModel;

class ResetPasswordModel extends Model
{
    protected $table = 'reset_password';
    protected $fillable = ['user_id' , 'code'];

    public function user()
    {
        return $this->hasOne(UserModel::class, 'user_id');
    }
}
