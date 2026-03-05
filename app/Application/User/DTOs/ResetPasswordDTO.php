<?php

namespace App\Application\User\DTOs;

class ResetPasswordDTO {

    public function __construct(
        public string $email,
        public string $newPassword
    )
    {}

}