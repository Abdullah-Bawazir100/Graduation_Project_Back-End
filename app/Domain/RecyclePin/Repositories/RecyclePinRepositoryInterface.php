<?php

namespace App\Domain\RecyclePin\Repositories;

use App\Domain\RecyclePin\Entities\RecyclePin;

interface RecyclePinRepositoryInterface
{
    /**
     * @return RecyclePin[]
     */
    public function getAll(): array;

    public function findById(int $id): ?RecyclePin;

    public function delete(int $id): void;
}
