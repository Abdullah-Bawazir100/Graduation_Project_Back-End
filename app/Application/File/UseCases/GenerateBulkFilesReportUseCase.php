<?php

namespace App\Application\File\UseCases;

use App\Application\Services\PdfReportService;
use App\Domain\Activity_Type\Repositories\Activity_Type_RepositoryInterface;
use App\Domain\Region\Repositories\RegionRepositoryInterface;
use App\Domain\District\Repositories\DistrictRepositoryInterface;

use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use DomainException;

class GenerateBulkFilesReportUseCase
{
    public function __construct(
        private ListFilesUseCase $listFilesUseCase,
        private Activity_Type_RepositoryInterface $activityTypeRepository,
        private RegionRepositoryInterface $regionRepository,
        private DistrictRepositoryInterface $districtRepository,
        private PdfReportService $pdf_report_service
    ) {}

    public function execute(int $authenticatedUserId, ?int $activityTypeId = null, ?int $regionId = null, ?int $districtId = null): string
    {
        $reportTitle = 'تقرير الملفات الضريبية';
        $reportSubtitle = 'سجل بجميع الملفات المتاحة حسب الصلاحيات';

        // Validation based on filters
        if ($activityTypeId !== null) {
            $activityType = $this->activityTypeRepository->findById($activityTypeId);
            if (!$activityType) {
                throw new DomainException("نوع النشاط المحدد غير موجود.");
            }
            $reportTitle = 'تقرير الملفات حسب النشاط';
            $reportSubtitle = 'النشاط: ' . $activityType->name;
        } elseif ($regionId !== null && $districtId !== null) {
            $region = $this->regionRepository->findById($regionId);
            if (!$region) {
                throw new DomainException("المنطقة المحددة غير موجودة.");
            }

            $district = $this->districtRepository->findById($districtId);
            if (!$district) {
                throw new DomainException("الحي المحدد غير موجود.");
            }

            if ($district->region->id !== $region->id) {
                throw new DomainException("الحي المحدد لا ينتمي للمنطقة المحددة.");
            }

            $reportTitle = 'تقرير الملفات حسب المنطقة و الحي';
            $reportSubtitle = 'المنطقة: ' . $region->name . ' - الحي: ' . $district->name;
        } elseif ($regionId !== null && $districtId === null) {
            $region = $this->regionRepository->findById($regionId);
            if (!$region) {
                throw new DomainException("المنطقة المحددة غير موجودة.");
            }

            $reportTitle = 'تقرير الملفات حسب المنطقة';
            $reportSubtitle = 'المنطقة: ' . $region->name;
        } elseif ($regionId === null && $districtId !== null) {
            throw new DomainException("يجب تحديد المنطقة أولاً عند اختيار الحي لإصدار التقرير.");
        }

        // Fetch files using ListFilesUseCase which handles roles natively
        $files = $this->listFilesUseCase->execute(null, $authenticatedUserId, $activityTypeId, $regionId, $districtId);

        if (empty($files)) {
            throw new DomainException("لا يوجد ملفات لعمل تقرير لها.");
        }

        $data = [
            'files' => $files,
            'reportTitle' => $reportTitle,
            'reportSubtitle' => $reportSubtitle
        ];

        $fileName = 'files_report_' . time() . '_' . Str::uuid() . '.pdf';

        return $this->pdf_report_service->generate(
            'reports.files-list',
            $data,
            $fileName,
            true // landscape
        );
    }
}
