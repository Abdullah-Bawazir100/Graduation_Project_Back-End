<?php

namespace App\Http\Controllers\Api;

use App\Application\Department\UseCases\CountDepartmentsUseCase;
use App\Application\Department\UseCases\GetDepartmentsStatisticsUseCase;
use App\Application\Activity_Type\UseCases\CountActivitiesTypesUseCase;
use App\Application\PaymentType\UseCases\CountPaymentsTypesUseCase;
use App\Application\Region\UseCases\CountRegionsUseCase;
use App\Application\District\UseCases\CountDistrictsUseCase;
use App\Application\File\UseCases\CountFilesUseCase;
use App\Application\File\UseCases\CountFilesByTypeUseCase;
use App\Application\FileMovement\UseCases\CountFileMovementsUseCase;
use App\Application\User\UseCases\CountUsersUseCase;
use App\Application\TaxCollector\UseCases\CountTaxCollectorsUseCase;
use App\Domain\TaxPayer\Enums\enFileType;
use App\Application\FileMovement\UseCases\GetFileMovementsStatisticsUseCase;
use App\Http\Responses\ApiResponse;
use MessageFormatter;

class StatisticsController
{
    public function __construct(
        private CountDepartmentsUseCase $countDepartmentsUseCase,
        private GetDepartmentsStatisticsUseCase $getDepartmentsStatisticsUseCase,
        private CountActivitiesTypesUseCase $countActivitiesTypesUseCase,
        private CountPaymentsTypesUseCase $countPaymentsTypesUseCase,
        private CountRegionsUseCase $countRegionsUseCase,
        private CountDistrictsUseCase $countDistrictsUseCase,
        private CountFilesUseCase $countFilesUseCase,
        private CountFilesByTypeUseCase $countFilesByTypeUseCase,
        private CountUsersUseCase $countUsersUseCase,
        private CountTaxCollectorsUseCase $countTaxCollectorsUseCase,
        private CountFileMovementsUseCase $countFileMovementsUseCase,
    ) {}

    public function getStatistics()
    {
        $statistics = [
            'overview' => [
                'departments_count' => $this->countDepartmentsUseCase->execute(),
                'activities_types_count' => $this->countActivitiesTypesUseCase->execute(),
                'payments_types_count' => $this->countPaymentsTypesUseCase->execute(),
                'regions_count' => $this->countRegionsUseCase->execute(),
                'districts_count' => $this->countDistrictsUseCase->execute(),
            ],
            'files_statistics' => [
                'total_files_count' => $this->countFilesUseCase->execute(),
                'individual_files_count' => $this->countFilesByTypeUseCase->execute(enFileType::Individual),
                'company_files_count' => $this->countFilesByTypeUseCase->execute(enFileType::Company),
                'charitable_company_files_count' => $this->countFilesByTypeUseCase->execute(enFileType::CharitableCompany),
            ],
            'users_statistics' => [
                'total_users_count' => $this->countUsersUseCase->execute(),
                'total_tax_collectors_count' => $this->countTaxCollectorsUseCase->execute(),
            ],
            'file_movements_statistics' => [
                'file_movement_count' => $this->countFileMovementsUseCase->execute(),
            ],
            'departments_statistics' => $this->getDepartmentsStatisticsUseCase->execute(),
        ];

        return ApiResponse::ok(
            data: $statistics,
            message: 'تم جلب الإحصائيات بنجاح.'
        );
    }
}
