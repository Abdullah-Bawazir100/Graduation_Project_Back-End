<?php

namespace App\Domain\RecyclePin\Entities;

class RecyclePin
{
    public function __construct(
        public ?int $id,
        public string $type,
        public array $data,
        public int $userId,
        public ?string $createdAt = null,
    ) {}
}
