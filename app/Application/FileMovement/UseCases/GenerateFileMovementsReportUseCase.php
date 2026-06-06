<?php

namespace App\Application\FileMovement\UseCases;

use App\Application\Services\PdfReportService;
use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use DomainException;

class GenerateFileMovementsReportUseCase
{
    public function __construct(
        private ListFilesMovementsUseCase $listFilesMovementsUseCase,
        private PdfReportService  $pdf_report_service
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

        return $this->pdf_report_service->generate(
            'reports.file-movements',
            $data,
            $fileName,
            true
        );
    }
}
