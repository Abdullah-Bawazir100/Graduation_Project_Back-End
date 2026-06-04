<?php

namespace App\Application\FileMovement\UseCases;

use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use DomainException;

class GenerateFileMovementsReportUseCase
{
    public function __construct(
        private ListFilesMovementsUseCase $listFilesMovementsUseCase
    ) {}

    public function execute(int $authenticatedUserId): string
    {
        $result = $this->listFilesMovementsUseCase->execute($authenticatedUserId);
        $filesMovements = $result['filesMovements'] ?? [];

        if (empty($filesMovements)) {
            throw new DomainException("لا توجد حركات ملفات لعرضها في التقرير.");
        }

        $data = [
            'filesMovements' => $filesMovements,
        ];

        $fileName = 'file_movements_report_' . time() . '_' . Str::uuid() . '.pdf';
        
        $directory = 'file-reports';
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $path = storage_path('app/public/' . $directory . '/' . $fileName);

        Pdf::view('reports.file-movements', $data)
            ->format('a4')
            ->landscape()
            ->save($path);

        return asset(Storage::url($directory . '/' . $fileName));
    }
}
