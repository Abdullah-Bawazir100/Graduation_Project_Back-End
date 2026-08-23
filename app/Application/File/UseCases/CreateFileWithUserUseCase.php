<?php

namespace App\Application\File\UseCases;

use App\Application\File\DTOs\FileDTOs;
use App\Application\User\DTOs\UserDTO;
use App\Domain\Activity_Type\Repositories\Activity_Type_RepositoryInterface;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Domain\File\Entities\File;
use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\FileStatus\Repositories\FileStatusRepositoryInterface;
use App\Domain\PaymentType\Repositories\PaymentTypeRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Interfaces\PasswordHashInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Jobs\SendWhatsAppMessageJob;
use Illuminate\Support\Str;
use DomainException;

class CreateFileWithUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $user_repository,
        private FileRepositoryInterface $file_repository,
        private DepartmentRepositoryInterface $department_repository,
        private PasswordHashInterface $password_hash,
        private FileStatusRepositoryInterface $file_status_repository,
        private PaymentTypeRepositoryInterface $payment_type_repository,
        private Activity_Type_RepositoryInterface $activity_Type_repository
    )
    {}

    public function execute(UserDTO $userDTO , FileDTOs $fileDTOs , int $authUserId)
    {
        $department = $this->department_repository->findById($userDTO->departmentID);
        if (!$department) {
            throw new DomainException("القسم مع ال ID [{$userDTO->departmentID}] غير موجود.");
        }

        $actor = $this->user_repository->findById($authUserId);

        if ($actor->role !== UserRole::Admin) {
            if ($actor->department->id !== $department->id) {
                throw new DomainException("لا يمكنك إنشاء ملف مكلف في قسم لا تنتمي إليه.");
            }
        }

        if($userDTO->role !== UserRole::Tax_Payer)
        {
            throw new DomainException('لا يمكن فتح ملف لمستخدم ليس مكلف.');
        }


        $userName = $userDTO->phone;
        $generatedPassword = Str::random(8);

        $user = new User(
            id: null,
            firstName: $userDTO->firstName,
            lastName: $userDTO->lastName,
            idCard: $userDTO->idCard ?? '',
            userName: $userName,
            phone: $userDTO->phone ?? '',
            image: $userDTO->image ?? '',
            password: $this->password_hash->hashPassword($generatedPassword),
            createdBy: $actor->id,
            department: $department,
            role: $userDTO->getRole(),
            mustChangePassword: true
        );

        $createdUser = $this->user_repository->create($user);


        $department = $this->department_repository->findById($fileDTOs->departmentId);
        $fileStatus = $this->file_status_repository->findById($fileDTOs->fileStatusId);
        $paymentType = $this->payment_type_repository->findById($fileDTOs->paymentTypeId);
        $activityType = $this->activity_Type_repository->findById($fileDTOs->activityTypeId);

        $file = new File(
            id: null,
            taxNumber: $fileDTOs->taxNumber,
            inventoryNumber: $fileDTOs->inventoryNumber,
            activityStartDate: $fileDTOs->activityStartDate,
            docsCount: $fileDTOs->docsCount,
            note: $fileDTOs->note,
            user: $createdUser,
            department: $department,
            fileStatus: $fileStatus,
            activityType: $activityType,
            paymentType: $paymentType,
            creator: $actor
        );

        $createdFile = $this->file_repository->create($file);

        $message = "مرحباً بك في نظام خدمات المكلفين.\n";
        $message .= "تم إنشاء حسابك وفتح ملفك بنجاح، بيانات الدخول الخاصة بك:\n\n";
        $message .= "اسم المستخدم: {$userName}\n";
        $message .= "كلمة المرور: {$generatedPassword}\n\n";
        $message .= "يمكنك تغيير كلمة المرور الخاصة بك من حسابك الشخصي عبر تطبيق الموبايل.";

        SendWhatsAppMessageJob::dispatch($userDTO->phone, $message);

        return [
            'fileInfo' => $createdFile,
            'userInfo' => $createdUser->toArray(),
        ];
    }
}
