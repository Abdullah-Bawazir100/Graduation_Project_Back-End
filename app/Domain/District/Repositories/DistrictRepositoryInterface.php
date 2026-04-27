<?php

namespace App\Domain\District\Repositories;

use App\Domain\District\Entities\District;

interface DistrictRepositoryInterface
{
    public function create(District $district);
    public function update(District $district);
    public function delete(int $id);
    public function getAll();
    public function findById(int $id);
    public function existsByName(string $name);
    public function countDistricts();
}
