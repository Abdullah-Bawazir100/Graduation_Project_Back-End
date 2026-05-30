<?php

namespace App\Domain\TaxPayerMobile\Repositories;

use App\Domain\TaxPayer\Entities\TaxPayer;
use App\Domain\User\Entities\User;

interface TaxPayerMobileRepositoryInterface
{
    public function create(User $user);
    public function update(User $user);
    public function getTaxPayerMobileFile(int $userId);
    public function getTaxPayerFileById(int $taxPayerId): ?TaxPayer;
}
