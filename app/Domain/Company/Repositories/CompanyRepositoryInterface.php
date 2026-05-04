<?php

namespace App\Domain\Company\Repositories;

use App\Domain\Company\Entities\Company;

interface CompanyRepositoryInterface
{
    public function create(Company  $company): Company;
    public function update(Company $company , int $id): ?Company;
    public function findById(int $id): ?Company;
    public function getAll();
    public function delete(int $id);

}
