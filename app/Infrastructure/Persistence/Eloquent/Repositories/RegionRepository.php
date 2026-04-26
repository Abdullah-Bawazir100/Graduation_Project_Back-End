<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Region\Entities\Region;
use App\Domain\Region\Repositories\RegionRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\RegionModel;

class RegionRepository implements RegionRepositoryInterface
{
    public function create(Region $region)
    {
        $regionModel = RegionModel::create([
            'name' => $region->name
        ]);

        return new Region(
            $regionModel->id,
            $regionModel->name
        );

    }
    public function update(Region $region)
    {

        $regionModel = RegionModel::find($region->id);

        if (!$regionModel) {
            throw new \Exception("No Region found with ID: [$region->id]");
        }

        $regionModel->name = $region->name;
        $regionModel->save();

        return new Region(
            $regionModel->id,
            $regionModel->name
        );

    }
    public function delete(int $id)
    {
        RegionModel::findOrFail($id)->delete();
    }
    public function getAll(){

        return RegionModel::all()
            ->map(fn ($regionModel) =>
                new Region(
                    $regionModel->id,
                    $regionModel->name
                )
            )
            ->toArray();

    }
    public function findById(int $id)
    {
        $regionModel = RegionModel::find($id);

        if(!$regionModel) return null;

        return new Region(
            $regionModel->id,
            $regionModel->name
        );

    }
    public function existsByName(string $name)
    {
        return RegionModel::where('name' , $name)->exists();
    }
}
