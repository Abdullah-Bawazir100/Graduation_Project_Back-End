<?php

namespace App\Application\File\UseCases;

use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use DomainException;

class GenerateFileReportUseCase
{
    public function __construct(
        private FileRepositoryInterface $fileRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(int $fileId, int $authenticatedUserId): string
    {
        $file = $this->fileRepository->findById($fileId);
        
        if (!$file) {
            throw new DomainException("الملف المطلوب غير موجود.");
        }

        $user = $this->userRepository->findById($authenticatedUserId);

        // التحقق من الصلاحيات: الأدمن يمكنه طباعة تقرير لأي ملف، أما المستخدم العادي لملفات قسمه فقط
        if ($user && $user->role !== UserRole::Admin) {
            if (!$user->department || $user->department->id !== $file->department->id) {
                throw new DomainException("لا تملك صلاحية لإنشاء تقرير لملف يتبع لقسم آخر غير قسمك.");
            }
        }
        
        $taxPayerUser = null;
        if ($file->taxPayer->userId) {
            $taxPayerUser = $this->userRepository->findById($file->taxPayer->userId);
        }

        if (!$taxPayerUser) {
            throw new DomainException("المستخدم الخاص بالمكلف غير موجود");
        }

        $data = [
            'file' => $file,
            'taxPayerUser' => $taxPayerUser,
            'taxPayer' => $file->taxPayer,
            'fileStatus' => $file->fileStatus,
        ];

        $fileName = 'file_report_' . $file->id . '_' . Str::uuid() . '.pdf';
        
        $directory = 'file-reports';
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $path = storage_path('app/public/' . $directory . '/' . $fileName);

        Pdf::view('reports.file', $data)
            ->format('a4')
            ->save($path);

        return asset(Storage::url($directory . '/' . $fileName));
    }
}
