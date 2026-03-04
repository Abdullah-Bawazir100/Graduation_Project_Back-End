<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Infrastructure\Persistence\Eloquent\Models\UserModel;
use App\Domain\User\Enums\UserRole;
use App\Domain\Department\Entities\Department;
use App\Infrastructure\Persistence\Eloquent\Models\DepartmentModel;

class UserRepository implements UserRepositoryInterface
{
    public function create(User $user): User
    {
        $userData = UserModel::create([
            'first_name'    => $user->firstName,
            'last_name'     => $user->lastName,
            'date_of_birth' => $user->dateOfBirth->format('Y-m-d'),
            'id_card'       => $user->idCard,
            'user_name'     => $user->userName,
            'email'         => $user->email,
            'phone'         => $user->phone,
            'password'      => bcrypt($user->password),
            'role'          => $user->role->value,
            'department_id' => $user->department->id,
            'created_by'    => $user->createdBy,
        ]);

        $userData->load('department');

        return $this->mapToDomain($userData);
    }

    public function update(User $user): User
    {
        $userData = UserModel::findOrFail($user->id);

        $userData->update([
            'first_name'    => $user->firstName,
            'last_name'     => $user->lastName,
            'date_of_birth' => $user->dateOfBirth->format('Y-m-d'),
            'id_card'       => $user->idCard,
            'user_name'     => $user->userName,
            'email'         => $user->email,
            'phone'         => $user->phone,
            'password'      => $user->password, // افتراضي: لا تغير إذا فارغ
            'role'          => $user->role->value,
            'department_id' => $user->department->id,
        ]);

        $userData->load('department');

        return $this->mapToDomain($userData);
    }

    public function delete(int $id): void
    {
        UserModel::findOrFail($id)->delete();
    }

    public function findById(int $id): ?User
    {
        $userData = UserModel::with('department')->find($id);

        if (!$userData) return null;

        return $this->mapToDomain($userData);
    }

    public function getAll(): array
    {
        $models = UserModel::with('department')->get();

        return $models->map(fn(UserModel $model) => $this->mapToDomain($model))->toArray();
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
            dateOfBirth: new \DateTime($userData->date_of_birth),
            idCard: $userData->id_card,
            userName: $userData->user_name,
            phone: $userData->phone,
            email: $userData->email,
            password: $userData->password,
            createdBy: $userData->created_by,
            department: $department,
            role: UserRole::from($userData->role),
        );
    }
}