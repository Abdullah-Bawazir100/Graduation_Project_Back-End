<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Address\Entities\Address;
use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\AddressModel;

class AddressRepository implements AddressRepositoryInterface
{
    public function create(Address $address)
    {
        $addressModel = AddressModel::create([
            'address' => $address->address,
            'district_id' => $address->district->id
        ]);

        return new Address(
            $addressModel->id,
            $addressModel->address,
            $address->region,
            $address->district
        );
    }

    public function update(Address $address)
    {
        $addressModel = AddressModel::find($address->id);

        if (!$addressModel) {
            throw new \Exception("No Address found with ID: [{$address->id}]");
        }

        $addressModel->update([
            'address' => $address->address,
            'district_id' => $address->district->id
        ]);

        return new Address(
            $addressModel->id,
            $addressModel->address,
            $address->region,
            $address->district
        );
    }

    public function delete(int $id)
    {
        $addressModel = AddressModel::find($id);

        if (!$addressModel) {
            throw new \Exception("No Address found with ID: [$id]");
        }

        $addressModel->delete();

        return true;
    }

    public function getAll()
    {
        $addresses = AddressModel::with('district.region')->get();

        return $addresses->map(function ($addressModel) {
            $region = new \App\Domain\Region\Entities\Region(
                $addressModel->district->region->id,
                $addressModel->district->region->name
            );

            $district = new \App\Domain\District\Entities\District(
                $addressModel->district->id,
                $addressModel->district->name,
                $region
            );

            return new Address(
                $addressModel->id,
                $addressModel->address,
                $region,
                $district
            );
        })->toArray();
    }

    public function findById(int $id)
    {
        $addressModel = AddressModel::with('district.region')->find($id);

        if (!$addressModel) {
            throw new \Exception("No Address found with ID: [$id]");
        }

        $region = new \App\Domain\Region\Entities\Region(
            $addressModel->district->region->id,
            $addressModel->district->region->name
        );

        $district = new \App\Domain\District\Entities\District(
            $addressModel->district->id,
            $addressModel->district->name,
            $region
        );

        return new Address(
            $addressModel->id,
            $addressModel->address,
            $region,
            $district
        );
    }
}