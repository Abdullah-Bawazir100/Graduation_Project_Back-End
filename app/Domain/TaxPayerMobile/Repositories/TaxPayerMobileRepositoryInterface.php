<?php

namespace App\Domain\TaxPayerMobile\Repositories;

use App\Domain\User\Entities\User;

interface TaxPayerMobileRepositoryInterface
{
    public function create(User $user);
    public function update(User $user);
}
