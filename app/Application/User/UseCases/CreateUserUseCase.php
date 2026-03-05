<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;
use App\Application\User\DTOs\UserDTO;
use App\Domain\User\Interfaces\PasswordHashInterface;
use DomainException;

class CreateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private DepartmentRepositoryInterface $departmentRepository,
        private PasswordHashInterface $passwordHash
    ) {}

    /**
     * @param User $actor Admin / Manager
     * @param UserDTO $dto جميع بيانات المستخدم التي أدخلها Admin / Manager
     * @return array معلومات المستخدم وكلمة المرور المؤقتة
     */
    public function execute(User $actor, UserDTO $dto): array
    {
        // 1️⃣ Authorization
        if (!in_array($actor->role, [UserRole::Admin, UserRole::Manager])) {
            throw new DomainException('Unauthorized: Only Admin or Manager can create users.');
        }

        // 2️⃣ التحقق من وجود القسم
        $department = $this->departmentRepository->findById($dto->departmentID);
        if (!$department) {
            throw new DomainException("Department with ID [{$dto->departmentID}] not found.");
        }

        // 3️⃣ توليد اسم المستخدم (userName) من الاسم الأول والاسم الأخير إذا لم يُعطَ
        $baseUserName = strtolower(trim($dto->firstName) . '.' . trim($dto->lastName));
        $userName = $dto->userName ?? $this->generateUniqueUserName($baseUserName);

        // 4️⃣ توليد كلمة مرور افتراضية
        $defaultPassword = '12345678';

        // 5️⃣ إنشاء كائن User كامل
        $user = new User(
            id: null,
            firstName: $dto->firstName,
            lastName: $dto->lastName,
            dateOfBirth: $dto->dateOfBirth,
            idCard: $dto->idCard ?? '',
            userName: $userName,
            phone: $dto->phone ?? '',
            password: $this->passwordHash->hashPassword($defaultPassword),
            createdBy: $actor->id,
            department: $department,
            role: $dto->role ? UserRole::from($dto->role) : UserRole::Employee,
            mustChangePassword: true
        );

        // 6️⃣ حفظ المستخدم في Repository
        $createdUser = $this->userRepository->create($user);

        // 7️⃣ إرجاع معلومات المستخدم وكلمة المرور المؤقتة
        return [
            'user_id' => $createdUser->id,
            'userName' => $userName,
            'temporaryPassword' => $defaultPassword,
            'mustChangePassword' => true,
            'user' => $createdUser
        ];
    }

    /**
     * توليد userName فريد
     */
    private function generateUniqueUserName(string $baseUserName): string
    {
        $userName = $baseUserName;
        $counter = 1;

        while ($this->userRepository->findByUserName($userName)) {
            $userName = $baseUserName . $counter;
            $counter++;
        }

        return $userName;
    }
}