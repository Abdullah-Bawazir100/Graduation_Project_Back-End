<?php

namespace App\Domain\ResetPassword\Entities;

class ResetPassword
{
    public function __construct(
        public ?int $id,
        public ?int $userId,
        public ?string $code
    )
    {
    }
}
