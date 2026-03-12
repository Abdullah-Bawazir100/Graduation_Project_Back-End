<?php

namespace App\Application\User\DTOs;

use App\Http\Requests\User\ResetPasswordRequest;

class ResetPasswordDTO {

    public function __construct(
        public string $newPassword
    )
    {}

    public static function fromRequest(ResetPasswordRequest $resetPasswordRequest): self
    {
        return new self(
            newPassword: $resetPasswordRequest->input('newPassword')
        );
    }

}