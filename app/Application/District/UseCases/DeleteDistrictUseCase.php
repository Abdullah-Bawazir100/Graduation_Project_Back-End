<?php

namespace App\Application\District\UseCases;

use App\Domain\District\Repositories\DistrictRepositoryInterface;

class DeleteDistrictUseCase
{
    public function __construct(
        private DistrictRepositoryInterface $district_repository_interface
    )
    {}

    public function execute(int $id)
    {
        $district = $this->district_repository_interface->findById($id);
        if(!$district)
        {
            throw new \DomainException("الحي مع ال ID [{$id}] غير موجود.");
        }
        $this->district_repository_interface->delete($id);
    }
}
