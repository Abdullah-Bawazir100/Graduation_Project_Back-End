<?php 

namespace App\Infrastructure\Persistence\Eloquent\Models;
use illuminate\Database\Eloquent\Model;
use App\Infrastructure\Persistence\Eloquent\Models\UserModel;

class DepartmentModel extends Model
{
    protected $table = 'departments';
    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(UserModel::class, 'department_id');
    }
}