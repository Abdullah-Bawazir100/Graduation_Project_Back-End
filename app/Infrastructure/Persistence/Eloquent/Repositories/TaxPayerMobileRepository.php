<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Department\Entities\Department;
use App\Domain\TaxPayerMobile\Repositories\TaxPayerMobileRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Infrastructure\Persistence\Eloquent\Models\UserModel;
use Override;

class TaxPayerMobileRepository implements TaxPayerMobileRepositoryInterface
{
    public function create(User $user)
    {

        $taxPayerData = UserModel::create([
            'first_name'    => $user->firstName,
            'last_name'     => $user->lastName,
            'id_card'       => $user->idCard,
            'user_name'     => $user->userName,
            'phone'         => $user->phone,
            'password'      => $user->password,
            'image'         => $user->image,
            'role'          => $user->role->value,
            'department_id' => $user->department->id,
            'created_by'    => $user->createdBy,
            'must_change_password' => $user->mustChangePassword
        ]);

        $taxPayerData->load('department');
        $taxPayerData->created_by = $taxPayerData->id;
        $taxPayerData->must_change_password = false;
        $taxPayerData->save();

        return $this->mapToDomain($taxPayerData);
    }
    public function update(User $user)
    {
        $taxPayerData = UserModel::findOrFail($user->id);

        $taxPayerData->update([
            'first_name'    => $user->firstName,
            'last_name'     => $user->lastName,
            'id_card'       => $user->idCard,
            'user_name'     => $user->userName,
            'phone'         => $user->phone,
            'image'         => $user->image,
            'password'      => $user->password,
            'role'          => $user->role->value,
            'department_id' => $user->department->id,
        ]);

        $taxPayerData->load('department');
        return $this->mapToDomain($taxPayerData);
    }
    private function mapToDomain(UserModel $userData): User
    {
        $department = new Department(
            id: $userData->department?->id ?? 0,
            name: $userData->department?->name ?? ''
        );


        return new User(
            id: $userData->id,
            firstName: $userData->first_name,
            lastName: $userData->last_name,
            idCard: $userData->id_card ?? '',
            userName: $userData->user_name,
            phone: $userData->phone,
            image: $userData->image ?? '',
            password: $userData->password,
            createdBy: $userData->created_by,
            department: $department,
            role: $userData->role,
            mustChangePassword: $userData->must_change_password ?? false
        );
    }
}
