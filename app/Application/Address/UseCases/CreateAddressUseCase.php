<?php

namespace App\Application\Address\UseCases;

use App\Application\Address\DTOs\AddressDTOs;
use App\Domain\Address\Entities\Address;
use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Domain\District\Repositories\DistrictRepositoryInterface;
use App\Domain\Region\Repositories\RegionRepositoryInterface;
use Exception;

class CreateAddressUseCase
{
    public function __construct(
        private AddressRepositoryInterface $address_repository_interface,
        private DistrictRepositoryInterface $district_repository_interface,
        private RegionRepositoryInterface $region_repository_interface
    )
    {}

    public function execute(AddressDTOs $addressDTOs)
    {
        $district = $this->district_repository_interface->findById($addressDTOs->districtID);
        if(!$district){
            throw new Exception ("الحي مع المعرف [{$addressDTOs->districtID}] غير موجود.");
        }

        $region = $this->region_repository_interface->findById($addressDTOs->regionID);
        if(!$region){
            throw new Exception ("المنطقة مع المعرف [{$addressDTOs->regionID}] غير موجودة.");
        }

        $address = $region->name . ' - ' . $district->name;

        return $this->address_repository_interface->create(
            new Address(null, $address, $region, $district)
        );
    }
}
