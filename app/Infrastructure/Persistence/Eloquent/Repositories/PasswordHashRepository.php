<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\User\Interfaces\PasswordHashInterface;
use Illuminate\Support\Facades\Hash;

class PasswordHashRepository implements PasswordHashInterface {

    public function hashPassword(string $password): string
    {
        return Hash::make($password);
    }

    public function verifyPassword(string $password, string $hashedPassword): bool
    {
        return Hash::check($password , $hashedPassword);
    }

}