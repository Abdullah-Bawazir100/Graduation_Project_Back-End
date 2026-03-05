<?php

namespace App\Application\User\DTOs;

class LoginDTO
{
    public function __construct(
        public string $userName,
        public string $password
    ) {}
}