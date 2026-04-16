<?php

namespace App\Application\Region\UseCases;

use App\Domain\Region\Repositories\RegionRepositoryInterface;

class ShowRegionUseCase
{
    public function __construct(
        private RegionRepositoryInterface $region_repository_interface
    )
    {}

    public function execute(int $id)
    {
        $region = $this->region_repository_interface->findById($id);

        if(!$region)
        {
            throw new \Exception("Region with ID [$id] not found.");
        }

        return $region;
    }
}
