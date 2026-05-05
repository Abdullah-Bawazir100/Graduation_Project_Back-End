<?php

namespace App\Domain\JobType\Repositories;

use App\Domain\JobType\Entities\JobType;

interface JobTypeRepositoryInterface
{
    public function create(JobType $jobType);
    public function update(JobType $jobType);
    public function delete(int $id);
    public function findById(int $id);
    public function getAll();
    public function existsByName(string $name);
    public function moveTaxCollectorsToAnotherJobType(int $oldJobTypeId , int $newJobTypeId);
}
