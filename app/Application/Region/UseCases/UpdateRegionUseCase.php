<?php

namespace App\Application\Region\UseCases;

use App\Application\Region\DTOs\RegionDTOs;
use App\Domain\Region\Entities\Region;
use App\Domain\Region\Repositories\RegionRepositoryInterface;

class UpdateRegionUseCase
{
    public function __construct(
        private RegionRepositoryInterface $region_repository_interface
    )
    {}

    public function execute(int $id , RegionDTOs $regionDTOs)
    {
        $regionData = $this->region_repository_interface->findById($id);
        if(!$regionData)
        {
            throw new \Exception('المنطقة مع ال ID [' . $id . '] غير موجودة.');
        }

        $name = $regionDTOs->name ?? $regionData->name;

        return $this->region_repository_interface->update(
            new Region($id , $name)
        );
    }
}
