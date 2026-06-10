<?php

namespace App\Application\Statistics\UseCases;

use App\Application\Activity_Type\UseCases\CountActivitiesTypesUseCase;
use App\Application\Department\UseCases\CountDepartmentsUseCase;
use App\Application\District\UseCases\CountDistrictsUseCase;
use App\Application\FileStatus\UseCases\CountFileStatusUseCase;
use App\Application\JobType\UseCases\CountJobTypesUseCase;
use App\Application\PaymentType\UseCases\CountPaymentsTypesUseCase;
use App\Application\Region\UseCases\CountRegionsUseCase;
use App\Application\Services\ActivityLogService;
use App\Domain\TaxPayer\Repositories\TaxPayerRepositoryInterface;
use App\Domain\Request\Repositories\TaxPayerRequestRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\File\Repositories\FileRepositoryInterface;
use App\Domain\Attachment\Repositories\AttachmentRepositoryInterface;
use App\Domain\FileMovement\Repositories\FileMovementRepositoryInterface;
use App\Domain\Notification\Enums\enNotificationType;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface;
use App\Domain\User\Enums\UserRole;
use App\Domain\TaxPayer\Enums\enFileType;

class GetSomeSectionsStatisticsUseCase
{
    public function __construct(
        private TaxPayerRepositoryInterface $tax_payer_repository,
        private TaxPayerRequestRepositoryInterface $request_repository,
        private UserRepositoryInterface $user_repository,
        private FileRepositoryInterface $file_repository,
        private FileMovementRepositoryInterface $file_movement_repository,
        private AttachmentRepositoryInterface $attachment_repository,
        private NotificationRepositoryInterface $notification_repository,
        private ActivityLogService $activityLogService,
        private TaxCollectorRepositoryInterface $tax_collector_repository,
        private CountDepartmentsUseCase $countDepartmentsUseCase,
        private CountActivitiesTypesUseCase $countActivitiesTypesUseCase,
        private CountPaymentsTypesUseCase $countPaymentsTypesUseCase,
        private CountRegionsUseCase $countRegionsUseCase,
        private CountDistrictsUseCase $countDistrictsUseCase,
        private CountFileStatusUseCase $countFileStatusUseCase,
        private CountJobTypesUseCase $countJobTypesUseCase,
    ) {}

    public function execute(int $authenticatedUserId): array
    {
        $actor = $this->user_repository->findById($authenticatedUserId);
        $actorRoleValue = $actor->role instanceof UserRole ? $actor->role->value : $actor->role;
        $isAdmin = $actorRoleValue === UserRole::Admin->value;
        $departmentId = $isAdmin ? null : (int) $actor->department->id;

        // ---- Files & Attachments Statistics ----
        $allAttachments = collect($this->attachment_repository->getAll());
        if (!$isAdmin) {
            $allAttachments = $allAttachments->filter(function ($attachment) use ($departmentId) {
                return $attachment->file->department->id === $departmentId;
            })->values();
        }

        $filesStats = [
            'total_files'              => $this->file_repository->countFiles($departmentId),
            'individual_files'         => $this->file_repository->countFilesByType(enFileType::Individual, $departmentId),
            'company_files'            => $this->file_repository->countFilesByType(enFileType::Company, $departmentId),
            'charitable_company_files' => $this->file_repository->countFilesByType(enFileType::CharitableCompany, $departmentId),
            'total_attachments' => $allAttachments->count(),
        ];

        // ---- Files Movements Statistics ----
        $filesMovements = $this->file_movement_repository->countFileMovements($departmentId);

        // ---- Requests Statistics ----
        $requestsStats = $this->request_repository->countRequests($departmentId);

        // ---- Users Statistics ----
        $usersStats = $this->user_repository->countUsers($departmentId);
        unset($usersStats['tax_collector_count']);

        // ---- TaxPayers Statistics ----
        $taxPayersStats = $this->tax_payer_repository->countTaxPayers($departmentId);

        // ---- Notifications Statistics ----
        $allNotifications = collect($this->notification_repository->getAll());

        if (!$isAdmin) {
            $allNotifications = $allNotifications->filter(function ($notification) use ($departmentId) {
                return $notification->sendBy->department->id === $departmentId;
            })->values();
        }

        $defaultTypeCounts = collect(enNotificationType::cases())
            ->mapWithKeys(function ($case) {
                return [$case->value => 0];
            });

        $typeCounts = $defaultTypeCounts->merge(
            $allNotifications->groupBy(function($notification) {
                return $notification->notificationType->value;
            })->map(function($group) {
                return $group->count();
            })
        )->all();

        $notificationsStats = [
            'total_notifications' => $allNotifications->count(),
            'type_counts' => $typeCounts,
        ];

        $activityLog = $this->activityLogService->getActivitySummary($departmentId);

        $taxCollectorsAndJobTypes = [
            'total_tax_collectors' => $this->tax_collector_repository->countTaxCollectors(),
            'total_job_types' => $this->countJobTypesUseCase->execute()
        ];

        $departments = $this->countDepartmentsUseCase->execute();
        $activitiesTypes = $this->countActivitiesTypesUseCase->execute();
        $paymentsTypes = $this->countPaymentsTypesUseCase->execute();
        $regions = $this->countRegionsUseCase->execute();
        $districts = $this->countDistrictsUseCase->execute();
        $filesStatus = $this->countFileStatusUseCase->execute();
        $statistics = [
            'departments_count' =>  $departments,
            'activities_types_count' => $activitiesTypes,
            'payments_types_count' => $paymentsTypes,
            'regions_count' => $regions,
            'districts_count' => $districts,
            'file_status_count' => $filesStatus,
        ];

        return [
            'overview' => $statistics,
            'files'         => $filesStats,
            'files_movements' => $filesMovements,
            'requests'      => $requestsStats,
            'users'         => $usersStats,
            'tax_collectors_and_job_types' => $taxCollectorsAndJobTypes,
            'tax_payers'    => $taxPayersStats,
            'notifications' => $notificationsStats,
            'activity_log' => $activityLog,
        ];
    }
}
