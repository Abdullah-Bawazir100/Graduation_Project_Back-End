<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\TaxCollector\Repositories\TaxCollectorRepositoryInterface;
use App\Domain\TaxCollector\Entities\TaxCollector;
use App\Infrastructure\Persistence\Eloquent\Models\TaxCollectorModel;
use App\Domain\Department\Entities\Department;
use App\Domain\JobType\Entities\JobType;


class TaxCollectorRepository implements TaxCollectorRepositoryInterface
{
    public function create(TaxCollector $taxCollector): TaxCollector
    {
        $taxCollectorModel = TaxCollectorModel::create([
            'full_name' => $taxCollector->fullName,
            'id_card' => $taxCollector->idCard,
            'phone' => $taxCollector->phone,
            'job_type_id' => $taxCollector->jobTypeId,
            'dept_id' => $taxCollector->deptID,
        ]);

        $taxCollectorModel->load('jobType', 'department');

        return $this->mapToDomain($taxCollectorModel);
    }

    public function update(TaxCollector $taxCollector): TaxCollector
    {
        $taxCollectorModel = TaxCollectorModel::find($taxCollector->id);

        $taxCollectorModel->update([
            'full_name' => $taxCollector->fullName,
            'id_card' => $taxCollector->idCard,
            'phone' => $taxCollector->phone,
            'job_type_id' => $taxCollector->jobTypeId,
            'dept_id' => $taxCollector->deptID,
        ]);

        $taxCollectorModel->load('jobType', 'department');

        return $this->mapToDomain($taxCollectorModel);
    }

    public function delete(int $id): void
    {
        $taxCollector = TaxCollectorModel::findOrFail($id);
        $taxCollector->delete();
    }

    public function findById(int $id): ?TaxCollector
    {
        $taxCollectorModel = TaxCollectorModel::with('jobType', 'department')->find($id);

        if (!$taxCollectorModel) {
            return null;
        }

        return $this->mapToDomain($taxCollectorModel);
    }

    public function getAll()
    {
        $taxCollectors = TaxCollectorModel::with('jobType' , 'department')->get();
        return $taxCollectors->map(fn(TaxCollectorModel $model) => $this->mapToDomain($model))->toArray();
    }

    public function findByName(string $name): ?TaxCollector
    {
        $taxCollectorModel = TaxCollectorModel::with('jobType', 'department')->where('full_name', $name)->first();

        if (!$taxCollectorModel) {
            return null;
        }

        return $this->mapToDomain($taxCollectorModel);
    }

    private function mapToDomain(TaxCollectorModel $model): TaxCollector
    {
        $jobType = new JobType(
            id: $model->jobType?->id ?? 0,
            name: $model->jobType?->name ?? ''
        );

        $department = new Department(
            id: $model->department?->id ?? 0,
            name: $model->department?->name ?? ''
        );

        return new TaxCollector(
            id: $model->id,
            fullName: $model->full_name,
            idCard: $model->id_card,
            phone: $model->phone,
            jobTypeId: $model->job_type_id,
            deptID: $model->dept_id,
            jobType: $jobType,
            department: $department,
        );
    }
}
