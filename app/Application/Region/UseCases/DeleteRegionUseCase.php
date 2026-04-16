<?php

namespace App\Application\Region\UseCases;

use App\Domain\Region\Repositories\RegionRepositoryInterface;

class DeleteRegionUseCase
{
    public function __construct(
        private RegionRepositoryInterface $region_repository_interface
    )
    {}

    public function execute(int $id)
    {
        $region = $this->region_repository_interface->findById($id);

        if (!$region) {
            throw new \DomainException("Region with ID [$id] not found.");
        }

        $this->region_repository_interface->delete($id);
    }
}
