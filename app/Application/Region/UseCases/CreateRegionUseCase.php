<?php

namespace App\Application\Region\UseCases;

use App\Application\Region\DTOs\RegionDTOs;
use App\Domain\Region\Entities\Region;
use App\Domain\Region\Repositories\RegionRepositoryInterface;

class CreateRegionUseCase
{
    public function __construct(
        private RegionRepositoryInterface $region_repository_interface
    )
    {}

    public function execute(RegionDTOs $regionDTOs)
    {
        $name = trim($regionDTOs->name);

        return $this->region_repository_interface->create(
            new Region(null , $name)
        );
    }
}
