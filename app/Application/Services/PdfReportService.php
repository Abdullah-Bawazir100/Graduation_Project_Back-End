<?php

namespace App\Application\Services;

use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

class PdfReportService
{
    public function generate(
        string $view,
        array $data,
        string $fileName,
        bool $landscape = false
    ): string {
        
        $directory = 'file-reports';

        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $path = storage_path(
            "app/public/{$directory}/{$fileName}"
        );

        $pdf = Pdf::view($view, $data)
            ->format('a4');

        if ($landscape) {
            $pdf->landscape();
        }

        $pdf->save($path);

        return asset(
            Storage::url("{$directory}/{$fileName}")
        );
    }
}
