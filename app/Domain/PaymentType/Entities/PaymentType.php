<?php

namespace App\Domain\PaymentType\Entities;

class PaymentType
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $note
    )
    {}
}
