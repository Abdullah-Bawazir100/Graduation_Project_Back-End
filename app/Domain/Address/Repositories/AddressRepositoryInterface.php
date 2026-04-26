<?php

namespace App\Domain\Address\Repositories;

use App\Domain\Address\Entities\Address;

interface AddressRepositoryInterface
{
    public function create(Address $address);
    public function update(Address $address);
    public function delete(int $id);
    public function getAll();
    public function findById(int $id);
}