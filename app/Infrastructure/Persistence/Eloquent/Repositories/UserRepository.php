<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Infrastructure\Persistence\Eloquent\Models\UserModel;
use App\Domain\User\Enums\UserRole;
use App\Domain\Department\Entities\Department;
use App\Infrastructure\Persistence\Eloquent\Models\TaxCollectorModel;

class UserRepository implements UserRepositoryInterface
{
    public function create(User $user): User
    {

        $userData = UserModel::create([
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

        $userData->load('department');

        return $this->mapToDomain($userData);
    }

    public function update(User $user): User
    {
        $userData = UserModel::findOrFail($user->id);

        $userData->update([
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

    public function findTaxPayerById(int $id): ?User
    {
        $userData = UserModel::with('department')->where('id' , $id)
                        ->where('role' , UserRole::Tax_Payer->value)->find($id);

        if (!$userData) return null;

        return $this->mapToDomain($userData);
    }

    public function findByUserName(string $user_name): ?User
    {
        $userData = UserModel::with('department')->where('user_name', $user_name)->first();

        if (!$userData) return null;

        return $this->mapToDomain($userData);
    }

    public function getAll(?string $search = null, ?int $departmentId = null): array
    {
        $query = UserModel::with('department');

        if ($departmentId !== null) {
            $query->where('department_id', $departmentId);
        }

        // اذا يوجد بحث
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', '%' . $search . '%')
                ->orWhere('last_name', 'LIKE', '%' . $search . '%');
            });
        }

        $userData = $query->get();

        return $userData->map(fn(UserModel $model) => $this->mapToDomain($model))->toArray();
    }

    public function updatePasswordOnly(int $userId, string $hashedPassword, bool $mustChangePassword = false): void
    {
        UserModel::where('id', $userId)->update([
            'password' => $hashedPassword,
            'must_change_password' => $mustChangePassword
        ]);
    }

    public function updatePassword(int $userId, string $newPassword, bool $mustChangePassword)
    {
        $userData = UserModel::findOrFail($userId);

        $userData->update([
            'password' => $newPassword,
            'must_change_password' => $mustChangePassword
        ]);

        $userData->load('department');
        return $this->mapToDomain($userData);
    }

    public function findByUserNameAndPhone(string $userName, string $phone): ?User
    {
        $userData = UserModel::where('user_name', $userName)
            ->where('phone', $phone)
            ->first();

        if (!$userData) {
            return null;
        }

        return $this->mapToDomain($userData);
    }

    public function countUsers(): array
    {
        return [
            'total_users' => UserModel::count(),

            'admin_count' => UserModel::where('role', UserRole::Admin->value)->count(),

            'manager_count' => UserModel::where('role', UserRole::Manager->value)->count(),

            'employee_count' => UserModel::where('role', UserRole::Employee->value)->count(),

            'tax_payer_count' => UserModel::where('role', UserRole::Tax_Payer->value)->count(),

            'collectors_manager_count' => UserModel::where('role', UserRole::Collectors_Manager->value)->count(),

            'tax_collector_count' => TaxCollectorModel::count(),
        ];
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
