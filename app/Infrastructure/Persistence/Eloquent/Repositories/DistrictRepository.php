<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\District\Entities\District;
use App\Domain\District\Repositories\DistrictRepositoryInterface;
use App\Domain\Region\Entities\Region;
use App\Infrastructure\Persistence\Eloquent\Models\DistrictModel;
use App\Infrastructure\Persistence\Eloquent\Models\RegionModel;

class DistrictRepository implements DistrictRepositoryInterface
{
    public function create(District $district)
    {
        $districtModel = DistrictModel::create([
            'name' => $district->name,
            'region_id' => $district->region->id
        ]);

        $districtModel->load('region');

        return $this->mapToDomain($districtModel);

    }
    public function update(District $district)
    {

        $districtModel = DistrictModel::find($district->id);

        $districtModel->update([
            'name' => $district->name,
            'region_id' => $district->region->id
        ]);

        $districtModel->load('region');

        return $this->mapToDomain($districtModel);

    }
    public function delete(int $id)
    {
        DistrictModel::findOrFail($id)->delete();
    }
    public function getAll(){

        $districts = DistrictModel::with('region')->get();
        return $districts->map(fn(DistrictModel $model) => $this->mapToDomain($model))->toArray();

    }
    public function findById(int $id)
    {
        $districtModel = DistrictModel::with('region')->find($id);
        if(!$districtModel)
            return null;

        return $this->mapToDomain($districtModel);

    }
    public function existsByName(string $name)
    {
        $districtData = DistrictModel::with('region')->where('name', $name)->first();
        if(!$districtData)
            return null;
        return $this->mapToDomain($districtData);
    }

    public function mapToDomain(DistrictModel $districtModel)
    {
        $region = new Region(
            id: $districtModel->region?->id ?? 0,
            name: $districtModel->region?->name ?? ''
        );

        return new District(
            id: $districtModel->id,
            name: $districtModel->name,
            region: $region
        );
    }
}
