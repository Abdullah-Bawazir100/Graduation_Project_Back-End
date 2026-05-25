<?php

namespace App\Http\Controllers\Api;

use App\Application\Department\UseCases\CountDepartmentsUseCase;
use App\Application\Activity_Type\UseCases\CountActivitiesTypesUseCase;
use App\Application\PaymentType\UseCases\CountPaymentsTypesUseCase;
use App\Application\Region\UseCases\CountRegionsUseCase;
use App\Application\District\UseCases\CountDistrictsUseCase;
use App\Application\File\UseCases\CountFilesUseCase;
use App\Application\User\UseCases\CountUsersUseCase;
use App\Application\TaxCollector\UseCases\CountTaxCollectorsUseCase;
use App\Http\Responses\ApiResponse;
use MessageFormatter;

class StatisticsController
{
    public function __construct(
        private CountDepartmentsUseCase $countDepartmentsUseCase,
        private CountActivitiesTypesUseCase $countActivitiesTypesUseCase,
        private CountPaymentsTypesUseCase $countPaymentsTypesUseCase,
        private CountRegionsUseCase $countRegionsUseCase,
        private CountDistrictsUseCase $countDistrictsUseCase,
        private CountFilesUseCase $countFilesUseCase,
        private CountUsersUseCase $countUsersUseCase,
        private CountTaxCollectorsUseCase $countTaxCollectorsUseCase
    ) {}

    public function getStatistics()
    {
        $statistics = [
            'departments_count' => $this->countDepartmentsUseCase->execute(),
            'activities_types_count' => $this->countActivitiesTypesUseCase->execute(),
            'payments_types_count' => $this->countPaymentsTypesUseCase->execute(),
            'regions_count' => $this->countRegionsUseCase->execute(),
            'districts_count' => $this->countDistrictsUseCase->execute(),
            'files_count' => $this->countFilesUseCase->execute(),
            'users_count' => $this->countUsersUseCase->execute(),
            'tax_collectors_count' => $this->countTaxCollectorsUseCase->execute(),
        ];

        return ApiResponse::ok(
            data: $statistics,
            message: 'تم جلب الإحصائيات بنجاح.'
        );
    }
}
