<?php

namespace App\Application\ResetPassword\DTOs;

class ResetPasswordDTOs
{
    public function __construct(
        public ?string $userName = null,
        public ?int $userId = null,
        public ?string $code = null,
        public ?string $newPassword = null,
    )
    {}
}
