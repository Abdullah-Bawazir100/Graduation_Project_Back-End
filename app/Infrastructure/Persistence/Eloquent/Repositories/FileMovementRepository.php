<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Activity_Type\Entities\Activity_Type;
use App\Domain\Department\Entities\Department;
use App\Domain\District\Entities\District;
use App\Domain\File\Entities\File;
use App\Domain\FileMovement\Entities\FileMovement;
use App\Domain\FileMovement\Repositories\FileMovementRepositoryInterface;
use App\Domain\FileStatus\Entities\FileStatus;
use App\Domain\PaymentType\Entities\PaymentType;
use App\Domain\Region\Entities\Region;
use App\Domain\TaxCollector\Entities\TaxCollector;
use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\User\Entities\User;
use App\Infrastructure\Persistence\Eloquent\Models\FileMovementModel;
use Illuminate\Support\Carbon;

class FileMovementRepository implements FileMovementRepositoryInterface
{
    public function create(FileMovement $fileMovement): FileMovement
    {
        $fileMovementModel = FileMovementModel::create([
            'file_id' => $fileMovement->file->id,
            'tax_collector_id' => $fileMovement->taxCollector->id,
            'status' => $fileMovement->status->value,
            'date' => $fileMovement->date,
            'department_id' => $fileMovement->department->id,
            'created_by' => $fileMovement->creator?->id,
        ]);

        $fileMovementModel->load('file', 'taxCollector', 'department', 'creator');

        return $this->mapToDomain($fileMovementModel);
    }

    public function update(FileMovement $fileMovement , int $id): ?FileMovement
    {
        $fileMovementModel = FileMovementModel::with(
            'file',
            'taxCollector',
            'department',
            'creator'
        )->find($id);

        if (!$fileMovementModel) {
            return null;
        }

        $fileMovementModel->update([
            'status' => $fileMovement->status,
            'date' => $fileMovement->date,
            'file_id' => $fileMovement->file->id,
            'tax_collector_id' => $fileMovement->taxCollector->id,
            'department_id' => $fileMovement->department->id,
            'created_by' => $fileMovement->creator?->id,
        ]);

        $fileMovementModel->refresh();

        $fileMovementModel->load(
            'file',
            'taxCollector',
            'department',
            'creator'
        );

        return $this->mapToDomain($fileMovementModel);
    }

    public function findById(int $id): ?FileMovement
    {
        $fileMovement = FileMovementModel::find($id);
        if(!$fileMovement)
        {
            return null;
        }
        return $this->mapToDomain($fileMovement);
    }

    public function findFileMovementByFileId(int $fileId): ?FileMovement
    {
        $fileMovement = FileMovementModel::with('file')
            ->where('file_id', $fileId)
            ->latest('id')
            ->first();

        return $fileMovement ? $this->mapToDomain($fileMovement) : null;
    }

    public function getAll()
    {
        $filesMovements = FileMovementModel::with(
            'file',
            'taxCollector',
            'department',
            'creator'
        )->get();
        return $filesMovements->map(fn(FileMovementModel $model) => $this->mapToDomain($model))->toArray();
    }

    public function delete(int $id): void
    {
        FileMovementModel::findOrFail($id)->delete();
    }

    public function getFileMovementCount(): int
    {
        return FileMovementModel::count();
    }

    public function getFileMovementsStatistics(): array
    { 
        $sixMonthsAgo = Carbon::now()->subMonths(6)->startOfMonth();

        $movements = FileMovementModel::where('date', '>=', $sixMonthsAgo)
            ->selectRaw("
                DATE_FORMAT(date, '%Y-%m') as month,
                status,
                COUNT(*) as count
            ")
            ->groupBy('month', 'status')
            ->orderBy('month')
            ->get();

        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $key = $month->format('Y-m');
            $monthlyData[$key] = [
                'month' => $key,
                'month_name' => $month->translatedFormat('F Y'),
                'inside_archive' => 0,
                'outside_archive' => 0,
                'missing' => 0,
                'total' => 0,
            ];
        }

        foreach ($movements as $movement) {
            if (!isset($monthlyData[$movement->month])) {
                continue;
            }

            $statusKey = match ($movement->status->value ?? $movement->status) {
                'InsideArchive' => 'inside_archive',
                'OutsideArchive' => 'outside_archive',
                'Missing' => 'missing',
                default => null,
            };

            if ($statusKey) {
                $monthlyData[$movement->month][$statusKey] = $movement->count;
                $monthlyData[$movement->month]['total'] += $movement->count;
            }
        }

        $totalInsideArchive = array_sum(array_column($monthlyData, 'inside_archive'));
        $totalOutsideArchive = array_sum(array_column($monthlyData, 'outside_archive'));
        $totalMissing = array_sum(array_column($monthlyData, 'missing'));
        $totalAll = $totalInsideArchive + $totalOutsideArchive + $totalMissing;

        return [
            'period' => [
                'from' => $sixMonthsAgo->format('Y-m-d'),
                'to' => Carbon::now()->format('Y-m-d'),
            ],
            'status_totals' => [
                'inside_archive' => $totalInsideArchive,
                'outside_archive' => $totalOutsideArchive,
                'missing' => $totalMissing,
                'total' => $totalAll,
            ],
            'monthly_breakdown' => array_values($monthlyData),
        ];
    }


    private function mapToDomain(FileMovementModel $model): FileMovement
    {
        $department = new Department(
            id: $model->department->id,
            name: $model->department->name,
        );

        $creator = $model->creator
            ? new User(
                id: $model->creator->id,
                firstName: $model->creator->first_name,
                lastName: $model->creator->last_name,
                idCard: $model->creator->id_card,
                userName: $model->creator->user_name,
                phone: $model->creator->phone,
                image: $model->creator->image,
                password: $model->creator->password,
                createdBy: $model->creator->created_by,
                department: $department,
                role: $model->creator->role,
                mustChangePassword: $model->creator->must_change_password,
            )
            : null;

        $fileRegion = new Region(
            id: $model->file->region->id,
            name: $model->file->region->name,
        );

        $fileDepartment = new Department(
            id: $model->file->department->id,
            name: $model->file->department->name,
        );

        return new FileMovement(
            id: $model->id,
            status: $model->status,
            date: $model->date,

            file: new File(
                id: $model->file->id,
                taxNumber: $model->file->tax_number,
                inventoryNumber: $model->file->inventory_number,
                activityStartDate: $model->file->activity_start_date,
                docsCount: $model->file->docs_count,
                note: $model->file->note,

                taxPayer: new TaxPayer(
                    id: $model->file->taxPayer->id,
                    userId: $model->file->taxPayer->user_id,
                    tradeName: $model->file->taxPayer->trade_name,
                    commercialRecord: $model->file->taxPayer->commercial_record,
                    activityLicense: $model->file->taxPayer->activity_license,
                    tradePict: $model->file->taxPayer->trade_pict,
                    insuranceCard: $model->file->taxPayer->insurance_card,
                    propertyDocPict: $model->file->taxPayer->property_doc_pict,
                    fileType: $model->file->taxPayer->file_type,
                    source: $model->file->taxPayer->source,
                ),

                department: $fileDepartment,

                fileStatus: new FileStatus(
                    id: $model->file->fileStatus->id,
                    statusName: $model->file->fileStatus->status_name,
                    statusDescription: $model->file->fileStatus->status_description,
                ),

                activityType: new Activity_Type(
                    id: $model->file->activityType->id,
                    name: $model->file->activityType->name,
                ),

                paymentType: new PaymentType(
                    id: $model->file->paymentType->id,
                    name: $model->file->paymentType->name,
                    note: $model->file->paymentType->note,
                ),

                region: $fileRegion,

                district: new District(
                    id: $model->file->district->id,
                    name: $model->file->district->name,
                    region: $fileRegion
                ),

                creator: $model->file->creator
                    ? new User(
                        id: $model->file->creator->id,
                        firstName: $model->file->creator->first_name,
                        lastName: $model->file->creator->last_name,
                        idCard: $model->file->creator->id_card,
                        userName: $model->file->creator->user_name,
                        phone: $model->file->creator->phone,
                        image: $model->file->creator->image,
                        password: $model->file->creator->password,
                        createdBy: $model->file->creator->created_by,
                        department: $fileDepartment,
                        role: $model->file->creator->role,
                        mustChangePassword: $model->file->creator->must_change_password,
                    )
                    : null,
            ),

            taxCollector: new TaxCollector(
                id: $model->taxCollector->id,
                fullName: $model->taxCollector->full_name,
                idCard: $model->taxCollector->id_card,
                phone: $model->taxCollector->phone,
                jobTypeId: $model->taxCollector->job_type_id,
                deptID: $model->taxCollector->dept_id,
            ),

            department: $department,

            creator: $creator,
        );
    }
}
