<?php

namespace App\Http\Controllers\Api;

use App\Application\Department\UseCases\CountDepartmentsUseCase;
use App\Application\Activity_Type\UseCases\CountActivitiesTypesUseCase;
use App\Application\PaymentType\UseCases\CountPaymentsTypesUseCase;
use App\Application\Region\UseCases\CountRegionsUseCase;
use App\Application\District\UseCases\CountDistrictsUseCase;
use App\Http\Responses\ApiResponse;
use MessageFormatter;

class StatisticsController
{
    public function __construct(
        private CountDepartmentsUseCase $countDepartmentsUseCase,
        private CountActivitiesTypesUseCase $countActivitiesTypesUseCase,
        private CountPaymentsTypesUseCase $countPaymentsTypesUseCase,
        private CountRegionsUseCase $countRegionsUseCase,
        private CountDistrictsUseCase $countDistrictsUseCase
    ) {}

    public function getStatistics()
    {
        $statistics = [
            'departments_count' => $this->countDepartmentsUseCase->execute(),
            'activities_types_count' => $this->countActivitiesTypesUseCase->execute(),
            'payments_types_count' => $this->countPaymentsTypesUseCase->execute(),
            'regions_count' => $this->countRegionsUseCase->execute(),
            'districts_count' => $this->countDistrictsUseCase->execute(),
        ];

        return ApiResponse::ok(
            data: $statistics,
            message: 'تم جلب الإحصائيات بنجاح.'
        );
    }
}
