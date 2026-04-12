<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;
use App\Domain\Activity_Type\Repositories\Activity_Type_RepositoryInterface;
use App\Domain\Activity_Type\Entities\Activity_Type;
use App\Infrastructure\Persistence\Eloquent\Models\ActivityTypeModel;

class Activity_Type_Repository implements Activity_Type_RepositoryInterface {

    public function create(Activity_Type $activity_Type)
    {
        $activityTypeModel = ActivityTypeModel ::create([
            'name' => $activity_Type->name
        ]);

        return new Activity_Type(
            $activityTypeModel->id,
            $activityTypeModel->name
        );

    }
    public function update(Activity_Type $activity_Type)
    {

        $activityTypeModel = ActivityTypeModel::find($activity_Type->id);

        if (!$activityTypeModel) {
            throw new \Exception("No Activity Type found with ID: [$activity_Type->id]");
        }

        $activityTypeModel->name = $activity_Type->name;
        $activityTypeModel->save();

        return new Activity_Type(
            $activityTypeModel->id,
            $activityTypeModel->name
        );

    }
    public function delete(int $id)
    {
        ActivityTypeModel::findOrFail($id)->delete();
    }
    public function getAll(){

        return ActivityTypeModel::all()
            ->map(fn ($activityTypeModel) =>
                new Activity_Type(
                    $activityTypeModel->id,
                    $activityTypeModel->name
                )
            )
            ->toArray();

    }
    public function findById(int $id)
    {
        $activityTypeModel = ActivityTypeModel::find($id);

        if(!$activityTypeModel) return null;

        return new Activity_Type(
            $activityTypeModel->id,
            $activityTypeModel->name
        );

    }
    public function existsByName(string $name)
    {
        return ActivityTypeModel::where('name' , $name)->exists();
    }

}
