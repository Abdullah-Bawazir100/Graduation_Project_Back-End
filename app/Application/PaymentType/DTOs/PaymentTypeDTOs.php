<?php

namespace App\Application\PaymentType\DTOs;

class PaymentTypeDTOs
{
    public function __construct(
        public ?string $name,
        public ?string $note
    )
    {}
}
