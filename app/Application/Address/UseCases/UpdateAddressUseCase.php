<?php

namespace App\Application\Address\UseCases;

use App\Application\Address\DTOs\AddressDTOs;
use App\Domain\Address\Entities\Address;
use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Domain\District\Repositories\DistrictRepositoryInterface;
use App\Domain\Region\Repositories\RegionRepositoryInterface;
use Exception;

class UpdateAddressUseCase
{
    public function __construct(
        private AddressRepositoryInterface $address_repository_interface,
        private DistrictRepositoryInterface $district_repository_interface,
        private RegionRepositoryInterface $region_repository_interface
    )
    {}

    public function execute(int $id, AddressDTOs $addressDTOs)
    {
        $currentAddress = $this->address_repository_interface->findById($id);
        if (!$currentAddress) {
            throw new Exception("العنوان مع ال ID [{$id}] غير موجود.");
        }

        $regionID = $addressDTOs->region_id ?? $currentAddress->region->id;
        $districtID = $addressDTOs->district_id ?? $currentAddress->district->id;

        $region = $this->region_repository_interface->findById($regionID);
        if (!$region) {
            throw new Exception("المنطقة مع ال ID [{$regionID}] غير موجودة.");
        }

        $district = $this->district_repository_interface->findById($districtID);
        if (!$district) {
            throw new Exception("الحي مع ال ID [{$districtID}] غير موجود.");
        }

        $newAddress = $region->name . ' - ' . $district->name;

        return $this->address_repository_interface->update(
            new Address(
                $id,
                $newAddress,
                $region,
                $district
            )
        );
    }
}
