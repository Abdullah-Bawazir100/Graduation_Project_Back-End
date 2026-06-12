<?php

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Application\User\DTOs\UserDTO;
use App\Domain\User\Interfaces\PasswordHashInterface;
use App\Domain\User\Enums\UserRole;
use App\Jobs\SendWhatsAppMessageJob;
use DomainException;
use Illuminate\Support\Str;

class CreateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private DepartmentRepositoryInterface $departmentRepository,
        private PasswordHashInterface $passwordHash
    ) {}

    /**
     * @param User $actor
     * @return array
     */

    public function execute(User $actor, UserDTO $userDTO)
    {
        $isAdmin = $actor->role === UserRole::Admin;

        if (
            !$isAdmin &&
            $userDTO->getRole() === UserRole::Admin
        ) {
            throw new DomainException(
                'غير مصرح لك بإنشاء مستخدم أدمن.'
            );
        }

        if (
            !$isAdmin &&
            $actor->department->id !== $userDTO->departmentID
        ) {
            throw new DomainException(
                'لا يمكنك إضافة مستخدمين لقسم غير القسم الذي تعمل فيه.'
            );
        }

        // Check if department exists
        $department = $this->departmentRepository->findById($userDTO->departmentID);
        if (!$department) {
            throw new DomainException("القسم مع ال ID [{$userDTO->departmentID}] غير موجود.");
        }

        $userName = $userDTO->phone;

        $isTaxPayer = $userDTO->getRole() === UserRole::Tax_Payer;
        $defaultPassword = $isTaxPayer ? Str::random(8) : '12345678';

        $user = new User(
            id: null,
            firstName: $userDTO->firstName,
            lastName: $userDTO->lastName,
            idCard: $userDTO->idCard ?? '',
            userName: $userName,
            phone: $userDTO->phone ?? '',
            image: $userDTO->image ?? '',
            password: $this->passwordHash->hashPassword($defaultPassword),
            createdBy: $actor->id,
            department: $department,
            role: $userDTO->getRole(),
            mustChangePassword: true
        );

        $createdUser = $this->userRepository->create($user);

        if ($isTaxPayer && $userDTO->phone) {
            $message = "مرحباً بك في نظام خدمات المكلفين.\n";
            $message .= "تم إنشاء حسابك بنجاح، بيانات الدخول الخاصة بك:\n\n";
            $message .= "اسم المستخدم: {$userName}\n";
            $message .= "كلمة المرور: {$defaultPassword}\n\n";
            $message .= "يمكنك تغيير إسم المستخدم و كلمة المرور الخاصة بك من حسابك الشخصي عبر تطبيق الموبايل.";

            SendWhatsAppMessageJob::dispatch($userDTO->phone, $message);
        }

        return [
            'user_id' => $createdUser->id,
            'userName' => $userName,
            'temporaryPassword' => $defaultPassword,
            'mustChangePassword' => true,
            'user' => $createdUser
        ];
    }


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
