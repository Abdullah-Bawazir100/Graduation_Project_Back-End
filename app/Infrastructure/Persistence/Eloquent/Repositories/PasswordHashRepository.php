<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\User\Interfaces\PasswordHashInterface;
use Illuminate\Support\Facades\Hash;

class PasswordHashRepository implements PasswordHashInterface {

    public function hashPassword(string $password): string
    {
        return Hash::make($password);
    }

    public function verifyPassword(string $plainPassword, string $hashedPassword): bool
    {
        return Hash::check($plainPassword , $hashedPassword);
    }

}
