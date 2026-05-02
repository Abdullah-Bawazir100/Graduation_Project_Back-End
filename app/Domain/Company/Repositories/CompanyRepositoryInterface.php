<?php

namespace App\Domain\Company\Repositories;

use App\Domain\Company\Entities\Company;

interface CompanyRepositoryInterface
{
    public function create(Company  $company): Company;
}
