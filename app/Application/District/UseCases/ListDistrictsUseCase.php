<?php

namespace App\Application\District\UseCases;

use App\Domain\District\Repositories\DistrictRepositoryInterface;

class ListDistrictsUseCase
{
    public function __construct(
        private DistrictRepositoryInterface $district_repository_interface
    )
    {}

    public function execute()
    {
        return $this->district_repository_interface->getAll();
    }
}
