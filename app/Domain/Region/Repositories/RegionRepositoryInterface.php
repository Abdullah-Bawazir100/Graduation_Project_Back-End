<?php

namespace App\Domain\Region\Repositories;

use App\Domain\Region\Entities\Region;

interface RegionRepositoryInterface
{
    public function create(Region $region);
    public function update(Region $region);
    public function delete(int $id);
    public function getAll();
    public function findById(int $id);
    public function existsByName(string $name);
}
