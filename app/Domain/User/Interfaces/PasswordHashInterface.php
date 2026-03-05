<?php

namespace App\Domain\User\Interfaces;

interface PasswordHashInterface {

    public function hashPassword(string $password): string;

    public function verifyPassword(string $password, string $hashedPassword): bool;

}