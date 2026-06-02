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
use App\Application\FileMovement\UseCases\GetTopDepartmentsMovementsStatisticsUseCase;
use App\Application\ActivityLog\UseCases\GetWeeklyActivityStatisticsUseCase;
use App\Application\FileStatus\UseCases\CountFileStatusUseCase;
use App\Domain\User\Enums\UserRole;
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
        private CountFileMovementsUseCase $countFileMovementsUseCase,
        private GetFileMovementsStatisticsUseCase $getFileMovementsStatisticsUseCase,
        private GetWeeklyActivityStatisticsUseCase $getWeeklyActivityStatisticsUseCase,
        private CountFileStatusUseCase $countFileStatusUseCase,
        private GetTopDepartmentsMovementsStatisticsUseCase $getTopDepartmentsMovementsStatisticsUseCase
    ) {}

    public function getStatistics()
    {
        $user = auth()->user();
        $departmentId = $user && $user->role !== UserRole::Admin ? $user->department_id : null;

        $statistics = [
            'overview' => [
                'departments_count' => $this->countDepartmentsUseCase->execute(),
                'activities_types_count' => $this->countActivitiesTypesUseCase->execute(),
                'payments_types_count' => $this->countPaymentsTypesUseCase->execute(),
                'regions_count' => $this->countRegionsUseCase->execute(),
                'districts_count' => $this->countDistrictsUseCase->execute(),
                'file_status_count' => $this->countFileStatusUseCase->execute()
            ],
            'files_statistics' => [
                'total_files_count' => $this->countFilesUseCase->execute($departmentId),
                'individual_files_count' => $this->countFilesByTypeUseCase->execute(enFileType::Individual, $departmentId),
                'company_files_count' => $this->countFilesByTypeUseCase->execute(enFileType::Company, $departmentId),
                'charitable_company_files_count' => $this->countFilesByTypeUseCase->execute(enFileType::CharitableCompany, $departmentId),
            ],
            'users_statistics' => [
                'total_users_count' => $this->countUsersUseCase->execute($departmentId),
            ],
            'file_movements_statistics' => [
                'file_movement_count' => $this->countFileMovementsUseCase->execute(),
                'last_6_months_statistics' => $this->getFileMovementsStatisticsUseCase->execute($departmentId),
                'top_departments_statistics' => $this->getTopDepartmentsMovementsStatisticsUseCase->execute(),
            ],
            'weekly_activity_statistics' => $this->getWeeklyActivityStatisticsUseCase->execute($departmentId),
            'departments_statistics' => $this->getDepartmentsStatisticsUseCase->execute(),
        ];

        return ApiResponse::ok(
            data: $statistics,
            message: 'تم جلب الإحصائيات بنجاح.'
        );
    }
}
