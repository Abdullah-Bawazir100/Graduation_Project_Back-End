<?php

namespace App\Application\User\DTOs;

use App\Http\Requests\User\LoginRequest;

class LoginDTO
{
    public function __construct(
        public string $userName,
        public string $password
    ) {}
    

    public static function fromRequest(LoginRequest $loginRequest): self
    {
        return new self(
            userName: $loginRequest->input('userName'),
            password: $loginRequest->input('password')
        );
    }
}