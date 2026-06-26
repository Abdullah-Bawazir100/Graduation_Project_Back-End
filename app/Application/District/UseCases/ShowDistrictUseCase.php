<?php

namespace App\Application\District\UseCases;

use App\Domain\District\Repositories\DistrictRepositoryInterface;
use DomainException;

class ShowDistrictUseCase
{
    public function __construct(
        private DistrictRepositoryInterface $district_repository_interface
    )
    {}

    public function execute(int $id)
    {
        $districtData = $this->district_repository_interface->findById($id);
        if(!$districtData){
            throw new DomainException("الحي مع ال ID [{$id}] غير موجود.");
        }
        return $districtData;
    }
}
