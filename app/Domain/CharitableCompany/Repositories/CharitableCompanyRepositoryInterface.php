<?php

namespace App\Domain\CharitableCompany\Repositories;

use App\Domain\CharitableCompany\Entities\CharitableCompany;

interface CharitableCompanyRepositoryInterface
{
    public function create(CharitableCompany $charitableCompany): CharitableCompany;
    public function update(CharitableCompany $charitableCompany , int $id): ?CharitableCompany;
    public function findById(int $id): ?CharitableCompany;
    public function getAll();
    public function delete(int $id);
    public function createCharitableCompanyFileToExistingTaxPayer(CharitableCompany $charitableCompany, int $userId);
}
