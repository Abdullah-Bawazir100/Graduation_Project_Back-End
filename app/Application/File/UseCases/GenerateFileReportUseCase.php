<?php

namespace App\Application\File\UseCases;

use App\Application\Services\PdfReportService;
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
        private UserRepositoryInterface $userRepository,
        private PdfReportService $pdf_report_service
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

        $taxPayerUser = $file->user;

        $data = [
            'file' => $file,
            'taxPayerUser' => $taxPayerUser,
            'fileStatus' => $file->fileStatus,
        ];

        $fileName = 'file_report_' . $file->id . '_' . Str::uuid() . '.pdf';

        return $this->pdf_report_service->generate(
            'reports.file',
            $data,
            $fileName
        );
    }
}
