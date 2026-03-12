<?php

namespace App\Application\User\DTOs;

use App\Http\Requests\User\SignUpRequest;

class SignUpDTO {

    public function __construct(
        public string $firstName,
        public string $lastName,
        public int $departmentID
    ) {}

    public static function fromRequest(SignUpRequest $signUpRequest): self
    {
        return new self(
            firstName: $signUpRequest->input('firstName'),
            lastName: $signUpRequest->input('lastName'),
            departmentID: $signUpRequest->input('departmentID')
        );
    }

}