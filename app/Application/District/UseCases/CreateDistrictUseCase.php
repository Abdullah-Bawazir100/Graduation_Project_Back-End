<?php

namespace App\Application\District\UseCases;

use App\Application\District\DTOs\DistrictDTOs;
use App\Domain\District\Entities\District;
use App\Domain\District\Repositories\DistrictRepositoryInterface;
use App\Domain\Region\Repositories\RegionRepositoryInterface;
use DomainException;

class CreateDistrictUseCase
{
    public function __construct(
        private DistrictRepositoryInterface $district_repository_interface,
        private RegionRepositoryInterface $region_repository_interface
    )
    {}

    public function execute(DistrictDTOs $districtDTOs)
    {
        $region = $this->region_repository_interface->findById($districtDTOs->regionID);
        if(!$region)
        {
            throw new DomainException("المنطقة مع ال ID [{$districtDTOs->regionID}] غير موجودة.");
        }

        $name = trim($districtDTOs->name);

        $district = new District(
            null,
            $name,
            $region
        );

        return $this->district_repository_interface->create($district);
    }
}
