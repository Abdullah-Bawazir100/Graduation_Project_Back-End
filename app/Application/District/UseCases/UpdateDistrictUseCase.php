<?php

namespace App\Application\District\UseCases;

use App\Application\District\DTOs\DistrictDTOs;
use App\Domain\District\Entities\District;
use App\Domain\District\Repositories\DistrictRepositoryInterface;
use App\Domain\Region\Repositories\RegionRepositoryInterface;

class UpdateDistrictUseCase
{
    public function __construct(
        private DistrictRepositoryInterface $districtRepositoryInterface,
        private RegionRepositoryInterface $region_repository_interface
    )
    {}

    public function execute(int $id , DistrictDTOs $districtDTOs)
    {
        $districtData = $this->districtRepositoryInterface->findById($id);
        if(!$districtData)
        {
            throw new \Exception('الحي مع ال ID [' . $id . '] غير موجود.');
        }

        if ($districtDTOs->regionID !== null) {

        $regionData = $this->region_repository_interface->findById($districtDTOs->regionID);
            if (!$regionData) {
                throw new \Exception('المنطقة مع ال ID [' . $districtDTOs->regionID . '] غير موجودة.');
            }
        }
        else {
            $regionData = $districtData->region;
        }

        $name = $districtDTOs->name ?? $districtData->name;
        $regionID = $districtDTOs->regionID ?? $regionData->id;

        return $this->districtRepositoryInterface->update(
            new District(
                id: $districtData->id,
                name: $name,
                region: $regionData
            )
        );
    }
}
