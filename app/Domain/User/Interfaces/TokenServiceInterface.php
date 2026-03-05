<?php

namespace App\Domain\User\Interfaces;
use App\Domain\User\Entities\User;

interface TokenServiceInterface {

    public function generateToken(User $user): string;
    public function removeToken(User $user);

}