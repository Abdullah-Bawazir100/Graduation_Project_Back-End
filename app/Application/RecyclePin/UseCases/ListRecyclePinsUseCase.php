<?php

namespace App\Application\RecyclePin\UseCases;

use App\Domain\RecyclePin\Repositories\RecyclePinRepositoryInterface;

class ListRecyclePinsUseCase
{
    public function __construct(
        private RecyclePinRepositoryInterface $repository
    )
    {
    }

    public function execute(): array
    {
        return $this->repository->getAll();
    }
}
