<?php

namespace App\Domain\Activity_Type\Repositories;

use App\Domain\Activity_Type\Entities\Activity_Type;

interface Activity_Type_RepositoryInterface{

    public function create(Activity_Type $activity_Type);
    public function update(Activity_Type $activity_Type);
    public function delete(int $id);
    public function getAll();
    public function findById(int $id);
    public function existsByName(string $name);

}
