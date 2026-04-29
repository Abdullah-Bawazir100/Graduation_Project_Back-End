<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\JobType\Entities\JobType;
use App\Domain\JobType\Repositories\JobTypeRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\JobTypeModel;

class JobTypeRepository implements JobTypeRepositoryInterface
{
    public function create(JobType $jobType): JobType
    {
        $jobTypeModel = JobTypeModel::create([
            'name' => $jobType->name,
        ]);

        return new JobType(
            $jobTypeModel->id,
            $jobTypeModel->name
        );
    }

    public function update(JobType $jobType): JobType
    {
        $jobTypeModel = JobTypeModel::find($jobType->id);

        if (!$jobTypeModel) {
            throw new \Exception("No job type found with ID: [$jobType->id]");
        }

        $jobTypeModel->name = $jobType->name;
        $jobTypeModel->save();

        return new JobType(
            $jobTypeModel->id,
            $jobTypeModel->name
        );
    }

    public function delete(int $id): void
    {
        JobTypeModel::findOrFail($id)->delete();
    }

    public function findById(int $id): ?JobType
    {
        $jobTypeModel = JobTypeModel::find($id);

        if (!$jobTypeModel) {
            return null;
        }

        return new JobType(
            $jobTypeModel->id,
            $jobTypeModel->name
        );
    }

    public function getAll(): array
    {
        return JobTypeModel::all()
            ->map(fn ($jobTypeModel) =>
                new JobType(
                    $jobTypeModel->id,
                    $jobTypeModel->name
                )
            )
            ->toArray();
    }

    public function existsByName(string $name): bool
    {
        return JobTypeModel::where('name', $name)->exists();
    }

}
