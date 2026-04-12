<?php

namespace App\Domain\Activity_Type\Entities;

class Activity_Type {
    public function __construct(
        public ?int $id,
        public string $name
    )
    { }
}
