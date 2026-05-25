<?php

namespace App\Application\ActivityLog\UseCases;

use App\Application\Services\ActivityLogService;

class GetWeeklyActivityStatisticsUseCase
{
    public function __construct(
        private ActivityLogService $activityLogService
    ) {}

    public function execute(): array
    {
        return $this->activityLogService->getWeeklyActivityStatistics();
    }
}
