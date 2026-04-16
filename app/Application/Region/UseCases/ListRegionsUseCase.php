<?php

namespace App\Application\Region\UseCases;

use App\Domain\Region\Repositories\RegionRepositoryInterface;

class ListRegionsUseCase
{
    public function __construct(
        private RegionRepositoryInterface $region_Repository_Interface
    )
    {}

    public function execute()
    {
        return $this->region_Repository_Interface->getAll();
    }
}
