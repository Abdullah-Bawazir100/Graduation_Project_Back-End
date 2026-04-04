<?php

namespace App\Application\User\Services;

use App\Domain\User\Entities\User;
use App\Domain\User\Enums\UserRole;

class UserAuthorizationService
{
    public function ensureCanManageUsers(User $actor): void
    {
        if (!in_array($actor->role, [UserRole::Admin->value, UserRole::Manager->value] , true)) {
            throw new \DomainException("Unauthorized action.");
        }
    }

    public function ensureCanDelete(User $actor): void
    {
        if ($actor->role !== UserRole::Admin->value) {
            throw new \DomainException("Only admin can delete users.");
        }
    }
}
